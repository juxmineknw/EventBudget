<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    // GET /api/team-members
    public function index()
    {
        return TeamMember::all();
    }

    // GET /api/team-members/{id}
    public function show($id)
    {
        return TeamMember::findOrFail($id);
    }

    // POST /api/team-members
    public function store(Request $request)
    {
        $data = $request->validate([
            'personalname'      => 'nullable|string|max:255',
            'given_name'        => 'nullable|string|max:255',
            'surname'           => 'nullable|string|max:255',
            'given_name_th'     => 'nullable|string|max:255',
            'surname_th'        => 'nullable|string|max:255',
            'passport_no'       => 'nullable|string|max:100',
            'nationality'       => 'nullable|string|max:100',
            'dob'               => 'nullable|date',
            'sex'               => 'nullable|string|max:50',
            'date_issue'        => 'nullable|date',
            'date_expiry'       => 'nullable|date',
            'personal_no'       => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:255',
            'passport_image'    => 'nullable|string',   // Base64
            'profile_image'     => 'nullable|string',   // Base64
        ]);

        // Save Base64 images
        if (!empty($data['passport_image'])) {
            $data['passport_image'] = $this->saveBase64Image($data['passport_image'], 'passports');
        }

        if (!empty($data['profile_image'])) {
            $data['profile_image'] = $this->saveBase64Image($data['profile_image'], 'profiles');
        }

        return TeamMember::create($data);
    }

    // PUT /api/team-members/{id}
    public function update(Request $request, $id)
    {
        $member = TeamMember::findOrFail($id);

        $data = $request->validate([
            // ... (validation อื่นๆ เหมือนเดิม) ...
            'passport_image'    => 'nullable',  // ตัด rule string ออกชั่วคราวเพื่อให้ยืดหยุ่น
            'profile_image'     => 'nullable',
            // ใส่ field อื่นๆ ให้ครบเหมือนเดิม
            'personalname'      => 'nullable|string|max:255',
            'given_name'        => 'nullable|string|max:255',
            'surname'           => 'nullable|string|max:255',
            'given_name_th'     => 'nullable|string|max:255',
            'surname_th'        => 'nullable|string|max:255',
            'passport_no'       => 'nullable|string|max:100',
            'nationality'       => 'nullable|string|max:100',
            'dob'               => 'nullable|date',
            'sex'               => 'nullable|string|max:50',
            'date_issue'        => 'nullable|date',
            'date_expiry'       => 'nullable|date',
            'personal_no'       => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:255',
        ]);

        // ------------------------------------------
        // แก้ไขส่วนจัดการรูป Passport
        // ------------------------------------------
        if (!empty($data['passport_image'])) {
            // เช็คว่าเป็น Base64 หรือไม่ (รูปใหม่ต้องขึ้นต้นด้วย data:image)
            if (preg_match('/^data:image\/(\w+);base64,/', $data['passport_image'])) {
                
                // ถ้ามีรูปเก่า ให้ลบทิ้งก่อน
                if ($member->passport_image && Storage::disk('public')->exists($member->passport_image)) {
                    Storage::disk('public')->delete($member->passport_image);
                }
                
                // บันทึกรูปใหม่
                $data['passport_image'] = $this->saveBase64Image($data['passport_image'], 'passports');
            } else {
                // ถ้าไม่ใช่ Base64 (แปลว่าเป็นชื่อไฟล์เดิม) -> ให้ลบออกจาก array data เพื่อไม่ให้มันไปทับค่าใน DB
                unset($data['passport_image']);
            }
        }

        // ------------------------------------------
        // แก้ไขส่วนจัดการรูป Profile (ใช้ logic เดียวกัน)
        // ------------------------------------------
        if (!empty($data['profile_image'])) {
            if (preg_match('/^data:image\/(\w+);base64,/', $data['profile_image'])) {
                
                if ($member->profile_image && Storage::disk('public')->exists($member->profile_image)) {
                    Storage::disk('public')->delete($member->profile_image);
                }

                $data['profile_image'] = $this->saveBase64Image($data['profile_image'], 'profiles');
            } else {
                unset($data['profile_image']);
            }
        }

        $member->update($data);
        return $member;
    }

    // DELETE /api/team-members/{id}
    public function destroy($id)
    {
        $member = TeamMember::findOrFail($id);

        if ($member->passport_image && Storage::disk('public')->exists($member->passport_image)) {
            Storage::disk('public')->delete($member->passport_image);
        }

        if ($member->profile_image && Storage::disk('public')->exists($member->profile_image)) {
            Storage::disk('public')->delete($member->profile_image);
        }

        $member->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // ============================================
    // 🔹 Base64 → File Helper Function
    // ============================================
    private function saveBase64Image($base64, $folder)
    {
        if (!$base64) return null;

        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $image = str_replace(' ', '+', $image);

        $fileName = $folder . '/' . uniqid() . '.png';

        Storage::disk('public')->put($fileName, base64_decode($image));

        return $fileName;
    }
}
