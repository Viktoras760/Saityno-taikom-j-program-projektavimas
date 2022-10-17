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

class LessonController extends Controller
{
    function getLesson($idSchool, $idFloor, $idClassroom, $id)
    {

        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
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

        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
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
            'Lower_grade_limit' => 'required|integer|max:12',
            'Upper_grade_limit' => 'required|integer|max:12',
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
        $lesson->save();
        return $lesson;
    }

    function registerToLesson(Request $request, $idSchool, $idFloor, $idClassroom, $id)
    {

        //
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
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

        $user = \App\Models\User::find($request->id_User);
        $lesson = \App\Models\Lesson::find($id);
        $userlessons = \App\Models\User::find($request->id_User)->lessons()->get();
        //return response()->json(['error' => 'This lesson does not suit your grade', $userlessons]);
        
        if ( $user->Grade < $lesson->Lower_grade_limit || $user->Grade > $lesson->Upper_grade_limit)
        {
            return response()->json(['error' => 'This lesson does not suit your grade']);
        }

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

        $lesson->users()->attach($user);
        return response()->json(['success' => 'Successfully registered']);
    }

    function deleteLesson($idSchool, $idFloor, $idClassroom, $id)
    {
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        $lesson = \App\Models\Lesson::find($id);
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

    function unregisterFromLesson(Request $request, $id)
    {
        
        $lesson = \App\Models\Lesson::find($id);
        
        $user = \App\Models\User::find($request->id_User);

        $lesson->users()->detach($user);
        return response()->json(['success' => 'Successfully unregistered']);
    }

    function updateLesson(Request $request, $idSchool, $idFloor, $idClassroom, $id)
    {
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        $lesson = \App\Models\Lesson::find($id);
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

    
}
