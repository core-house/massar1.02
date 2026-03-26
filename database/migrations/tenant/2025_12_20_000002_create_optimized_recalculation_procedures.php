<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 1) Recalculate Average Cost (Single Item)
        |--------------------------------------------------------------------------
        */
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_average_cost');

        DB::unprepared(<<<'SQL'
        CREATE PROCEDURE sp_recalculate_average_cost(
            IN p_item_id INT,
            IN p_from_date DATE
        )
        BEGIN
            DECLARE v_running_qty DECIMAL(15,4) DEFAULT 0;
            DECLARE v_running_value DECIMAL(15,4) DEFAULT 0;
            DECLARE v_new_average DECIMAL(15,4) DEFAULT 0;

            SELECT 
                IFNULL(SUM(oi.qty_in - oi.qty_out), 0),
                IFNULL(SUM(
                    CASE 
                        WHEN oi.qty_in > 0 THEN oi.detail_value
                        WHEN oi.qty_out > 0 THEN -oi.detail_value
                        ELSE 0
                    END
                ), 0)
            INTO v_running_qty, v_running_value
            FROM operation_items oi
            INNER JOIN operhead oh ON oi.pro_id = oh.id
            WHERE oi.item_id = p_item_id
                AND oi.is_stock = 1
                AND oi.pro_tybe IN (11,12,20,59)
                AND oh.isdeleted = 0
                AND (p_from_date IS NULL OR oh.pro_date >= p_from_date);

            IF v_running_qty > 0 THEN
                SET v_new_average = v_running_value / v_running_qty;
            ELSE
                SET v_new_average = 0;
            END IF;

            UPDATE items 
            SET average_cost = v_new_average,
                updated_at = NOW()
            WHERE id = p_item_id;
        END
        SQL);

        /*
        |--------------------------------------------------------------------------
        | 2) Recalculate Average Cost (Batch)
        |--------------------------------------------------------------------------
        */
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_average_cost_batch');

        DB::unprepared(<<<'SQL'
        CREATE PROCEDURE sp_recalculate_average_cost_batch(
            IN p_item_ids TEXT,
            IN p_from_date DATE
        )
        BEGIN
            UPDATE items i
            INNER JOIN (
                SELECT 
                    oi.item_id,
                    IFNULL(SUM(oi.qty_in - oi.qty_out),0) as total_qty,
                    IFNULL(SUM(
                        CASE 
                            WHEN oi.qty_in > 0 THEN oi.detail_value
                            WHEN oi.qty_out > 0 THEN -oi.detail_value
                            ELSE 0
                        END
                    ),0) as total_value
                FROM operation_items oi
                INNER JOIN operhead oh ON oi.pro_id = oh.id
                WHERE FIND_IN_SET(oi.item_id, p_item_ids)
                    AND oi.is_stock = 1
                    AND oi.pro_tybe IN (11,12,20,59)
                    AND oh.isdeleted = 0
                    AND (p_from_date IS NULL OR oh.pro_date >= p_from_date)
                GROUP BY oi.item_id
            ) calculated ON i.id = calculated.item_id
            SET i.average_cost = 
                CASE 
                    WHEN calculated.total_qty > 0
                    THEN calculated.total_value / calculated.total_qty
                    ELSE 0
                END;

            UPDATE items
            SET average_cost = 0
            WHERE FIND_IN_SET(id, p_item_ids)
                AND id NOT IN (
                    SELECT DISTINCT oi.item_id
                    FROM operation_items oi
                    INNER JOIN operhead oh ON oi.pro_id = oh.id
                    WHERE FIND_IN_SET(oi.item_id, p_item_ids)
                        AND oi.is_stock = 1
                        AND oi.pro_tybe IN (11,12,20,59)
                        AND oh.isdeleted = 0
                        AND (p_from_date IS NULL OR oh.pro_date >= p_from_date)
                );
        END
        SQL);

        /*
        |--------------------------------------------------------------------------
        | 3) Recalculate Profit (Single Invoice)
        |--------------------------------------------------------------------------
        */
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_profit');

        DB::unprepared(<<<'SQL'
        CREATE PROCEDURE sp_recalculate_profit(
            IN p_operation_id INT
        )
        BEGIN
            DECLARE v_total_profit DECIMAL(15,2) DEFAULT 0;
            DECLARE v_total_cost DECIMAL(15,2) DEFAULT 0;
            DECLARE v_fat_disc DECIMAL(15,2);
            DECLARE v_fat_total DECIMAL(15,2);
            DECLARE v_pro_type INT;
            DECLARE v_pro_value DECIMAL(15,2);

            SELECT fat_disc, fat_total, pro_type, pro_value
            INTO v_fat_disc, v_fat_total, v_pro_type, v_pro_value
            FROM operhead
            WHERE id = p_operation_id;

            IF v_pro_type IN (10,12,19,59) THEN
                UPDATE operation_items oi
                INNER JOIN items i ON oi.item_id = i.id
                SET oi.cost_price = COALESCE(i.average_cost,0)
                WHERE oi.pro_id = p_operation_id
                    AND oi.is_stock = 1
                    AND (v_pro_type != 59 OR oi.qty_out > 0);
            END IF;

            UPDATE operation_items
            SET profit = (
                ((detail_value - 
                 CASE 
                    WHEN v_fat_disc > 0 AND v_fat_total > 0
                    THEN (detail_value * v_fat_disc / v_fat_total)
                    ELSE 0
                 END
                ) * IFNULL(currency_rate,1))
                -
                (cost_price * ABS(qty_out - qty_in))
            )
            WHERE pro_id = p_operation_id
                AND is_stock = 1;

            SELECT IFNULL(SUM(cost_price * ABS(qty_out - qty_in)),0)
            INTO v_total_cost
            FROM operation_items
            WHERE pro_id = p_operation_id
                AND is_stock = 1;

            SET v_total_profit = IFNULL(v_pro_value,0) - v_total_cost;

            IF v_pro_type = 12 THEN
                SET v_total_profit = -v_total_profit;
            END IF;

            UPDATE operhead
            SET profit = v_total_profit,
                fat_cost = v_total_cost
            WHERE id = p_operation_id;
        END
        SQL);

        /*
        |--------------------------------------------------------------------------
        | 4) Recalculate Profits Batch
        |--------------------------------------------------------------------------
        */
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_profits_batch');

        DB::unprepared(<<<'SQL'
        CREATE PROCEDURE sp_recalculate_profits_batch(
            IN p_item_ids TEXT,
            IN p_from_date DATE
        )
        BEGIN
            DECLARE done INT DEFAULT FALSE;
            DECLARE v_operation_id INT;

            DECLARE cur CURSOR FOR
                SELECT DISTINCT oh.id
                FROM operhead oh
                INNER JOIN operation_items oi ON oh.id = oi.pro_id
                WHERE FIND_IN_SET(oi.item_id, p_item_ids)
                    AND oi.is_stock = 1
                    AND oh.pro_type IN (10,12,13,19,59)
                    AND oh.pro_date >= p_from_date
                    AND oh.isdeleted = 0
                ORDER BY oh.pro_date, oh.id;

            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

            OPEN cur;

            read_loop: LOOP
                FETCH cur INTO v_operation_id;
                IF done THEN
                    LEAVE read_loop;
                END IF;

                CALL sp_recalculate_profit(v_operation_id);
            END LOOP;

            CLOSE cur;
        END
        SQL);

        /*
        |--------------------------------------------------------------------------
        | 5) Master Procedure
        |--------------------------------------------------------------------------
        */
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_all_after_operation');

        DB::unprepared(<<<'SQL'
        CREATE PROCEDURE sp_recalculate_all_after_operation(
            IN p_item_ids TEXT,
            IN p_from_date DATE
        )
        BEGIN
            CALL sp_recalculate_average_cost_batch(p_item_ids, p_from_date);
            CALL sp_recalculate_profits_batch(p_item_ids, p_from_date);
        END
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_average_cost');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_average_cost_batch');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_profit');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_profits_batch');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalculate_all_after_operation');
    }
};