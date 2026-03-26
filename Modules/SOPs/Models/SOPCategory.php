declare(strict_types=1);

namespace Modules\SOPs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SOPCategory extends Model
{
    use HasFactory;

    protected $table = 'sop_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function sops(): HasMany
    {
        return $this->hasMany(SOP::class, 'category_id');
    }
}
