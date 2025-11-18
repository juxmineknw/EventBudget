namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTeam extends Model
{
    protected $fillable = [
        'event_id',
        'member_name'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
