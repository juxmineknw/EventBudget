namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function show($id)
    {
        $event = Event::with(['details', 'team'])->findOrFail($id);

        return response()->json($event);
    }
}
