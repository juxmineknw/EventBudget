<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Routing\Controller;

class EventController extends Controller
{
    // 1. ดึงข้อมูลทั้งหมด
    public function index()
    {
        // ดึงข้อมูล Event ทั้งหมด (ถ้ามีตาราง team เชื่อมกันให้ใส่ with('team') ด้วย)
        $events = Event::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $events
        ]);
    }

    // 2. ดูรายตัว (ใช้ในหน้า Details)
    public function show($id)
    {
        // ลองดึงแบบมี team ด้วย (ถ้าคุณทำ Relation ไว้ใน Model)
        // ถ้ายังไม่ได้ทำ Relation ให้ลบ ->with('team') ออก
        $event = Event::with('team')->findOrFail($id);

        return response()->json([
            'data' => $event
        ]);
    }

    // 3. สร้างใหม่
    public function store(Request $request)
    {
        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'client_name' => $request->client_name,
            'location' => $request->location,
            'total_budget' => $request->total_budget,
            'venue_name' => $request->venue_name,
            'venue_url' => $request->venue_url,
            'accommodation_name' => $request->accommodation_name,
            'accommodation_url' => $request->accommodation_url,
            'drive_link' => $request->drive_link,
        ]);

        // TODO: ถ้าต้องการบันทึก Team ด้วย ต้องเขียนเพิ่มตรงนี้ (เช่น loop ข้อมูลทีมแล้ว create ลงตาราง teams)

        return response()->json(['data' => $event], 201);
    }

    // 4. แก้ไข (ฟังก์ชันที่คุณขาดไป)
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $event->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'client_name' => $request->client_name,
            'location' => $request->location,
            'total_budget' => $request->total_budget,
            'venue_name' => $request->venue_name,
            'venue_url' => $request->venue_url,
            'accommodation_name' => $request->accommodation_name,
            'accommodation_url' => $request->accommodation_url,
            'drive_link' => $request->drive_link,
        ]);

        return response()->json(['data' => $event]);
    }

    // 5. ลบ
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Deleted successfully'], 204);
    }
}