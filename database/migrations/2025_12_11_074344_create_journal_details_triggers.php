<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\DB;



return new class extends Migration

{

    public function up(): void
    {
        // 1. نمسح أي حاجة قديمة أولاً (مهم جدًا للتطوير)
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_update_parent_balances');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_journal_details_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_journal_details_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_journal_details_after_delete');

        // 2. Stored Procedure بسيطة وسريعة: تطلع من الحساب لفوق وتحدث كل أب
        DB::unprepared('
            CREATE PROCEDURE sp_update_parent_balances(IN start_account_id BIGINT)
            BEGIN
                DECLARE current_id BIGINT DEFAULT start_account_id;
                DECLARE parent_id_val BIGINT;
                
                loop_label: REPEAT
                    SELECT parent_id INTO parent_id_val
                    FROM acc_head
                    WHERE id = current_id AND (isdeleted = 0 OR isdeleted IS NULL);
                    
                    IF parent_id_val IS NULL THEN
                        LEAVE loop_label;
                    END IF;
                    
                    UPDATE acc_head AS parent
                    SET balance = (
                        SELECT COALESCE(SUM(balance), 0)
                        FROM acc_head
                        WHERE parent_id = parent_id_val
                          AND (isdeleted = 0 OR isdeleted IS NULL)
                    )
                    WHERE parent.id = parent_id_val;
                    
                    SET current_id = parent_id_val;
                UNTIL parent_id_val IS NULL END REPEAT loop_label;
            END
        ');
        // 3. Trigger بعد الإضافة
        DB::unprepared('
            CREATE TRIGGER trg_journal_details_after_insert
            AFTER INSERT ON journal_details
            FOR EACH ROW
            BEGIN
                UPDATE acc_head
                SET balance = (
                    SELECT COALESCE(SUM(CAST(debit AS DECIMAL(18,3))), 0) - 
                           COALESCE(SUM(CAST(credit AS DECIMAL(18,3))), 0)
                    FROM journal_details
                    WHERE account_id = NEW.account_id
                      AND (isdeleted = 0 OR isdeleted IS NULL)
                )
                WHERE id = NEW.account_id;
                CALL sp_update_parent_balances(NEW.account_id);
            END
        ');
        // 4. Trigger بعد التعديل
        DB::unprepared('
            CREATE TRIGGER trg_journal_details_after_update
            AFTER UPDATE ON journal_details
            FOR EACH ROW
            BEGIN
                IF OLD.account_id != NEW.account_id OR OLD.account_id IS NULL OR NEW.account_id IS NULL THEN
                    IF OLD.account_id IS NOT NULL THEN
                        UPDATE acc_head SET balance = (
                            SELECT COALESCE(SUM(CAST(debit AS DECIMAL(18,3))), 0) - 
                                   COALESCE(SUM(CAST(credit AS DECIMAL(18,3))), 0)
                            FROM journal_details
                            WHERE account_id = OLD.account_id
                              AND (isdeleted = 0 OR isdeleted IS NULL)
                        ) WHERE id = OLD.account_id;
                        CALL sp_update_parent_balances(OLD.account_id);
                    END IF;
                    IF NEW.account_id IS NOT NULL THEN
                        UPDATE acc_head SET balance = (
                            SELECT COALESCE(SUM(CAST(debit AS DECIMAL(18,3))), 0) - 
                                   COALESCE(SUM(CAST(credit AS DECIMAL(18,3))), 0)
                            FROM journal_details
                            WHERE account_id = NEW.account_id
                              AND (isdeleted = 0 OR isdeleted IS NULL)
                        ) WHERE id = NEW.account_id;
                        CALL sp_update_parent_balances(NEW.account_id);
                    END IF;
                ELSE
                    UPDATE acc_head SET balance = (
                        SELECT COALESCE(SUM(CAST(debit AS DECIMAL(18,3))), 0) - 
                               COALESCE(SUM(CAST(credit AS DECIMAL(18,3))), 0)
                        FROM journal_details
                        WHERE account_id = NEW.account_id
                          AND (isdeleted = 0 OR isdeleted IS NULL)
                    ) WHERE id = NEW.account_id;
                    CALL sp_update_parent_balances(NEW.account_id);
                END IF;
            END
        ');

        // 5. Trigger بعد الحذف
        DB::unprepared('
            CREATE TRIGGER trg_journal_details_after_delete
            AFTER DELETE ON journal_details
            FOR EACH ROW
            BEGIN
                UPDATE acc_head SET balance = (
                    SELECT COALESCE(SUM(CAST(debit AS DECIMAL(18,3))), 0) - 
                           COALESCE(SUM(CAST(credit AS DECIMAL(18,3))), 0)
                    FROM journal_details
                    WHERE account_id = OLD.account_id
                      AND (isdeleted = 0 OR isdeleted IS NULL)
                ) WHERE id = OLD.account_id;
                CALL sp_update_parent_balances(OLD.account_id);
            END
        ');
    }


    public function down(): void

    {

        DB::unprepared('DROP TRIGGER IF EXISTS trg_journal_details_after_insert');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_journal_details_after_update');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_journal_details_after_delete');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_update_parent_balances');

    }

};