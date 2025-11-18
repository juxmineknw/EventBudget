namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_name',
        'start_date',
        'end_date',
        'client',
        'location',
        'total_budget',
        'description'
    ];

    public function details()
    {
        return $this->hasOne(EventDetail::class);
    }

    public function team()
    {
        return $this->hasMany(EventTeam::class);
    }
}
