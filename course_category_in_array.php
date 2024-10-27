public function getStudentInfo(Request $request)
    {
        $studentId = $request->input('student_id');

        $student = Student::where('id', $studentId)->first();
        $categoryId = $student->category_id;

        $tutors = Tutor::all();


        $filteredTutors = $tutors->filter(function ($tutor) use ($categoryId) {
               
            $categories = json_decode($tutor->course_category_id, true);
           
            return is_array($categories) && in_array($categoryId, $categories);
        });

        $userIds = $filteredTutors->pluck('user_id')->unique();
        $users = User::whereIn('id', $userIds)->pluck('name', 'id');

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json([
            'roll_no' => $student->roll_no,
            'category_id' => $student->category_id,
            'course_id' => $student->course_id,
            'users' => $users->map(function ($name, $id) {
                return ['id' => $id, 'name' => $name];
            })->toArray() ?? [], // Ensure users is an array, not null
        ]);
        
    }
