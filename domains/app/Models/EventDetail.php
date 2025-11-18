namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDetail extends Model
{
    protected $fillable = [
        'event_id',
        'venue_name',
        'venue_url',
        'accommodation_name',
        'accommodation_url',
        'drive_url'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
