<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Floor;
use App\Models\School;
use App\Models\Classroom;
use Illuminate\Support\Carbon;
use Validator;
use App\Http\Controllers\AuthController;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    function getLesson($idSchool, $idFloor, $idClassroom, $id)
    {
        $role = (new AuthController)->authRole();
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher' || $role == 'Pupil') && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to get lessons in this school',
            ], 401);
        }
        $classroom = \App\Models\Classroom::find($idClassroom);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $FloorsClassroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('id_Classroom', '=', $idClassroom)->get();
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if(!$classroom) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor'], 404);
        }


        $lesson = \App\Models\Lesson::find($id);
        if ($role == 'Pupil' && auth()->user()->Grade < $lesson->Lower_grade_limit || $lesson->Upper_grade_limit < auth()->user()->Grade)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson is not suitable for your grade',
            ], 401);
        }
        if(!$lesson) {
            return response()->json(['error' => 'Lesson not found'], 404);
        }
        if($lesson->fk_Classroomid_Classroom != $idClassroom)
        {
            return response()->json(['error' => 'Lesson is in another classroom'], 404);
        }
        return $lesson;
    }

    function addLesson(Request $req, $idSchool, $idFloor, $idClassroom)
    {

        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator'&& $role != 'Teacher')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher') && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to add lessons in this school',
            ], 401);
        }
        $classroom = \App\Models\Classroom::find($idClassroom);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $FloorsClassroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('id_Classroom', '=', $idClassroom)->get();
        $lessons = \App\Models\Lesson::where('fk_Classroomid_Classroom', '=', $idClassroom)->get();
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if(!$classroom) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor'], 404);
        }

        if((new Carbon($req->input('Lessons_starting_time')))->gt(new Carbon($req->input('Lessons_ending_time'))))
        {
            return response()->json(['error' => 'Incorrect lesson time'], 404);
        }
        if (!count($lessons) < 1)
        {
            for ($i = 0; $i < count($lessons); $i++)
            {
                //      12:00-12:45
                //11:15-12:00
                
                if((new Carbon($req->input('Lessons_starting_time')))->eq(new Carbon($lessons[$i]->Lessons_starting_time)) || (new Carbon($req->input('Lessons_ending_time')))->eq(new Carbon($lessons[$i]->Lessons_ending_time)) || (new Carbon($req->input('Lessons_ending_time')))->eq(new Carbon($lessons[$i]->Lessons_starting_time)) || (new Carbon($req->input('Lessons_starting_time')))->eq(new Carbon($lessons[$i]->Lessons_ending_time)))
                {
                    return response()->json(['error' => 'This time is already occupied by another lesson'], 404);
                }
                //12:00  -  12:45
                //  12:15-12:30
                if(((new Carbon($req->input('Lessons_starting_time'))) < (new Carbon($lessons[$i]->Lessons_starting_time)) && (new Carbon($req->input('Lessons_ending_time'))) > (new Carbon($lessons[$i]->Lessons_ending_time)))||((new Carbon($req->input('Lessons_starting_time'))) > (new Carbon($lessons[$i]->Lessons_starting_time)) && (new Carbon($req->input('Lessons_ending_time'))) < (new Carbon($lessons[$i]->Lessons_ending_time))))
                {
                    return response()->json(['error' => 'This time is already occupied by another lesson'], 404);
                }
                //12:00-12:45
                //  12:00-13:00
                if(((new Carbon($req->input('Lessons_starting_time'))) > (new Carbon($lessons[$i]->Lessons_starting_time)) && (new Carbon($req->input('Lessons_starting_time'))) < (new Carbon($lessons[$i]->Lessons_ending_time))) ||(new Carbon($req->input('Lessons_ending_time')) > (new Carbon($lessons[$i]->Lessons_starting_time)) && (new Carbon($req->input('Lessons_ending_time'))) < (new Carbon($lessons[$i]->Lessons_ending_time))))
                {
                    return response()->json(['error' => 'This time is already occupied by another lesson'], 404);
                }
            }
        }

        $validator = Validator::make($req->all(), [
            'Lessons_name' => 'required|string|max:255',
            'Lower_grade_limit' => 'required|integer|max:12|min:0',
            'Upper_grade_limit' => 'required|integer|max:12|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $lesson = new Lesson;
        $lesson->Lessons_name= $req->input('Lessons_name');
        $lesson->Lessons_starting_time= $req->input('Lessons_starting_time');
        $lesson->Lessons_ending_time= $req->input('Lessons_ending_time');
        $lesson->Lower_grade_limit= $req->input('Lower_grade_limit');
        $lesson->Upper_grade_limit= $req->input('Upper_grade_limit');
        $lesson->fk_Classroomid_Classroom= $idClassroom;
        $lesson->creator_id= auth()->user()->id_User;
        $lesson->save();
        return $lesson;
    }

    function registerToLesson($idSchool, $idFloor, $idClassroom, $id)
    {

        $role = (new AuthController)->authRole();
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher' || $role == 'Pupil') && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to register to lessons in this school',
            ], 401);
        }
        $classroom = \App\Models\Classroom::find($idClassroom);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $FloorsClassroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('id_Classroom', '=', $idClassroom)->get();
        $lessons = \App\Models\Lesson::where('fk_Classroomid_Classroom', '=', $idClassroom)->get();
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if(!$classroom) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor'], 404);
        }
        //

        $user = auth()->user();
        $lesson = \App\Models\Lesson::find($id);
        if(!$lesson) {
            return response()->json(['error' => 'Lesson not found'], 404);
        }
        if ($role == 'Pupil' && auth()->user()->Grade < $lesson->Lower_grade_limit || $lesson->Upper_grade_limit < auth()->user()->Grade)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson is not suitable for your grade',
            ], 401);
        }
        $userlessons = auth()->user()->lessons()->get();
        //return response()->json(['error' => 'This lesson does not suit your grade', $userlessons]);
        
        /*if ( $user->Grade < $lesson->Lower_grade_limit || $user->Grade > $lesson->Upper_grade_limit)
        {
            return response()->json(['error' => 'This lesson does not suit your grade']);
        }*/

        for($i = 0; $i < count($userlessons); $i++)
        {

            //      12:00-12:45
                //11:15-12:00
                
            if((new Carbon($lesson->Lessons_starting_time))->eq(new Carbon($userlessons[$i]->Lessons_starting_time)) || (new Carbon($lesson->Lessons_ending_time))->eq(new Carbon($userlessons[$i]->Lessons_ending_time)) || (new Carbon($lesson->Lessons_ending_time))->eq(new Carbon($userlessons[$i]->Lessons_starting_time)) || (new Carbon($lesson->Lessons_starting_time))->eq(new Carbon($userlessons[$i]->Lessons_ending_time)))
            {
                return response()->json(['error' => 'You already have lesson on this time'], 404);
            }
            //12:00  -  12:45
            //  12:15-12:30
            if(((new Carbon($lesson->Lessons_starting_time)) < (new Carbon($userlessons[$i]->Lessons_starting_time)) && (new Carbon($lesson->Lessons_ending_time)) > (new Carbon($userlessons[$i]->Lessons_ending_time)))||((new Carbon($lesson->Lessons_starting_time)) > (new Carbon($userlessons[$i]->Lessons_starting_time)) && (new Carbon($lesson->Lessons_ending_time)) < (new Carbon($userlessons[$i]->Lessons_ending_time))))
            {
                return response()->json(['error' => 'You already have lesson on this time']);
            }
            //12:00-12:45
            //  12:00-13:00
            if(((new Carbon($lesson->Lessons_starting_time)) > (new Carbon($userlessons[$i]->Lessons_starting_time)) && (new Carbon($lesson->Lessons_starting_time)) < (new Carbon($userlessons[$i]->Lessons_ending_time))) ||(new Carbon($lesson->Lessons_ending_time) > (new Carbon($userlessons[$i]->Lessons_starting_time)) && (new Carbon($lesson->Lessons_ending_time)) < (new Carbon($userlessons[$i]->Lessons_ending_time))))
            {
                return response()->json(['error' => 'You already have lesson on this time'], 404);
            }
        }

        $lesson->users()->attach(auth()->user());
        return response()->json(['success' => 'Successfully registered']);
    }

    function deleteLesson($idSchool, $idFloor, $idClassroom, $id)
    {
        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator'&& $role != 'Teacher')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher' ) && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to delete lessons in this school',
            ], 401);
        }
        $lesson = \App\Models\Lesson::find($id);
        if (!$lesson)
        {
            return response()->json(['error' => 'Lesson not found'], 404);
        }
        if ($role == 'Teacher' && auth()->user()->id_User != $lesson->creator_id)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to delete another teacher lesson',
            ], 401);
        }
        $classroom = \App\Models\Classroom::find($idClassroom);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $FloorsClassroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('id_Classroom', '=', $idClassroom)->get();
        $lessonUsers = \App\Models\Lesson::find($id)->users()->get();
        
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if(!$classroom) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school. Cannot delete'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor. Cannot delete'], 404);
        }
        if(count($lessonUsers))
        {
            return response()->json(['error' => 'Lesson has users registered'], 404);
        }

        $lesson->delete();
        return response()->json(['success' => 'Lesson deleted']);
    }

    function unregisterFromLesson($id)
    {
        $lesson = \App\Models\Lesson::find($id);

        $lesson->users()->detach(auth()->user());
        return response()->json(['success' => 'Successfully unregistered']);
    }

    function updateLesson(Request $request, $idSchool, $idFloor, $idClassroom, $id)
    {
        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator'&& $role != 'Teacher')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher' ) && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to add lessons in this school',
            ], 401);
        }
        
        $lesson = \App\Models\Lesson::find($id);
        if (!$lesson)
        {
            return response()->json(['error' => 'Lesson not found'], 404);
        }
        if ($role == 'Teacher' && auth()->user()->id_User != $lesson->creator_id)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to delete another teacher lesson',
            ], 401);
        }
        $classroom = \App\Models\Classroom::find($idClassroom);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $FloorsClassroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('id_Classroom', '=', $idClassroom)->get();
        $lessonUsers = \App\Models\Lesson::find($id)->users()->get();
        
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if(!$classroom) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor'], 404);
        }
        if(count($lessonUsers) && $request->Lower_grade_limit != $lesson->Lower_grade_limit)
        {
            return response()->json(['error' => 'Lesson has users registered. Cannot change grade'], 404);
        }

        $lesson->update([
            'Lessons_name' => $request->Lessons_name,
            'Lessons_starting_time' => $request->Lessons_starting_time,
            'Lessons_ending_time' => $request->Lessons_ending_time,
            'Lower_grade_limit' => $request->Lower_grade_limit,
            'Upper_grade_limit' => $request->Upper_grade_limit
        ]);
        return response()->json(['success' => 'Lesson updated']);
    }

    function getUserLessons()
    {
        $userlessons = \App\Models\User::find(auth()->user()->id_User)->lessons()->get();
        if (count($userlessons) < 1)
        {
            return response()->json(['error' => 'user has no lessons'], 404);
        }
        return $userlessons;
    }



    
}
