declare(strict_types=1);

namespace Modules\SOPs\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Models\Department;

class SOP extends Model
{
    use HasFactory;

    protected $table = 'sops';

    protected $fillable = [
        'title',
        'category_id',
        'department_id',
        'description',
        'content',
        'attachment',
        'version',
        'status',
        'created_by',
        'updated_by',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SOPCategory::class, 'category_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
