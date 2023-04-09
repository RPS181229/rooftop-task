<?php

namespace App\Http\Controllers;

use App\Models\AppointmentBooking;
use App\Models\Coach;
use App\Models\CoachSchedule;
use App\Models\Timezone;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class TaskController extends Controller
{


    /**
     * converToTz is an helper function used for converting time from one timezone to anther
     * @param string $time
     * @param string $fromTz
     * @param string $toTz
     * @return string $time
     *  
     * */
    function converToTz($time, $fromTz, $toTz)
    {
        // timezone by php friendly values
        $date = new DateTime($time, new DateTimeZone($fromTz));
        $date->setTimezone(new DateTimeZone($toTz));
        $time = $date->format('Y-m-d H:i:s');
        return $time;
    }


    /**
     * timeToSlots is a helper function used for providing time slots 
     * @param string $startTime
     * @param string $endTime
     * @param string $busySlots
     * @return Array
     * */

    function timeToSlots($startTime, $endTime, $busySlots = [])
    {
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);
        $slot = strtotime(date('H:i:s', $startTime) . ' +30 minutes');

        $data = [];

        for ($i = 0; $slot <= $endTime; $i++) {
            $isNotAvailable = array_search(date('H:i:s', $startTime), array_column($busySlots, 'start'));

            //Skip if slot not available
            if (!is_int($isNotAvailable)) {
                $data[$i] = [
                    'start' => date('H:i:s', $startTime),
                    'end' => date('H:i:s', $slot),
                ];
            }

            $startTime = $slot;
            $slot = strtotime(date('H:i:s', $startTime) . ' +30 minutes');
        }

        return $data;
    }


    /**
     * getAvailableCoaches method is used for find availables coaches of the particular date
     * @param string $timezone
     * @param string $start_date_time
     * 
     * */

    public function getAvailableCoaches(Request $request)
    {
        $requestdTimezone = $request->timezone;
        $requestedDateTime = $request->start_date_time;

        //Get All Timezones
        $timezones = Timezone::get();

        $availableCoachIds = [];
        foreach ($timezones as $timezone) {
            $timeInRequiredTz = $this->converToTz($requestedDateTime, $requestdTimezone, $timezone->name);

            //get day in coach's timezone
            $requiredDay = date('l', strtotime($timeInRequiredTz));

            //get time in coach's timezone
            $requiredTime = date('H:i:s', strtotime($timeInRequiredTz));

            //Get IDs of available coaches
            $coachs = CoachSchedule::where('day_of_week', $requiredDay)
                ->whereTime('available_at', '<=', $requiredTime)
                ->whereTime('available_until', '>=', $requiredTime)
                ->pluck('coach_id')->toArray();

            $availableCoachIds = array_merge($availableCoachIds, $coachs);
        }

        $availableCoachIds = array_unique($availableCoachIds);

        $availableCoaches = Coach::select('id', 'name')->whereIn('id', $availableCoachIds)->get();



        if (!empty($availableCoaches)) {
            return [
                'status' => 1,
                'message' => 'Available coaches found successfully',
                'data' => $availableCoaches
            ];
        }

        return [
            'status' => 1,
            'message' => 'No record found',
            'data' => (object)null
        ];
    }

    /**
     * getAvailableSloats method is used for getting available time slots of a particular coach
     * 
     * 
     * */ 
    public function getAvailableSloats(Request $request)
    {
        $requestdTimezone = $request->timezone;
        $requestedDateTime = $request->appoitment_at;

        $coach = Coach::where('id', $request->coach_id)->first();
        $dateTimeInRequiredTz = $this->converToTz($requestedDateTime, $coach->timezone->name, $requestdTimezone);

        //get day in coach's timezone
        $requiredDay = date('l', strtotime($dateTimeInRequiredTz));


        //get time in coach's timezone
        $requiredTime = date('H:i:s', strtotime($dateTimeInRequiredTz));

        //Check whether coach is available on requeted date and before the end of appoitment until
        $coachSchedule = CoachSchedule::where('day_of_week', $requiredDay)
            // ->whereTime('available_at', '<=', $requiredTime)
            ->whereTime('available_until', '>=', $requiredTime)
            ->first();

        if (empty($coachSchedule)) {
            return [
                'status' => 1,
                'message' => 'Coach is not available for requested date and time',
                'data' => (object)null
            ];
        }

        $bookedAppoitments = AppointmentBooking::select(['appointment_at', 'appointment_until'])->where('coach_id', $coach->id)->whereDate('appointment_at', date('Y-m-d', strtotime($dateTimeInRequiredTz)))->get();

        $availableSlots = $busySlots = [];
        $bookedAppoitment = empty($bookedAppoitment) ? [] :  $bookedAppoitment->toArray();
        foreach ($bookedAppoitments as $key => $bookedAppoitment) {
            $busySlots[$key]['start'] = date('H:i:s', strtotime($this->converToTz($bookedAppoitment['appointment_at'], $requestdTimezone, $coach->timezone->name)));
            $busySlots[$key]['end'] = date('H:i:s', strtotime($this->converToTz($bookedAppoitment['appointment_until'], $requestdTimezone, $coach->timezone->name)));
        }

        if (strtotime($coachSchedule->available_at) < strtotime($requiredTime))
            $availableSlots = $this->timeToSlots($requiredTime, $coachSchedule->available_until, $busySlots);
        else
            $availableSlots = $this->timeToSlots($coachSchedule->available_at, $coachSchedule->available_until, $busySlots);


        if (!empty($availableSlots)) {
            return [
                'status' => 1,
                'message' => 'Available slots found successfully',
                'data' => $availableSlots
            ];
        }

        return [
            'status' => 1,
            'message' => 'No record found',
            'data' => (object)null
        ];
    }

    /**
     *  This methos is used for book slop
     * @param $coach_id, $timezone, $appotment_at
     * 
     * */
    public function bookSlot(Request $request)
    {
        $requestdTimezone = $request->timezone;
        $requestedDateTime = $request->appoitment_at;

        $coach = Coach::where('id', $request->coach_id)->first();
        $dateTimeInRequiredTz = $this->converToTz($requestedDateTime, $coach->timezone->name, $requestdTimezone);

        //get day in coach's timezone
        $requiredDay = date('l', strtotime($dateTimeInRequiredTz));

        //get time in coach's timezone
        $requiredTime = date('H:i:s', strtotime($dateTimeInRequiredTz));

        $coachSchedule = CoachSchedule::where('day_of_week', $requiredDay)
            ->whereTime('available_until', '>=', $requiredTime)
            ->first();

        $alreadyScheduled = AppointmentBooking::where('coach_id', $coach->id)->where('appointment_at', $dateTimeInRequiredTz)->first();

        if (empty($coachSchedule) || !empty($alreadyScheduled)) {
            return [
                'status' => 1,
                'message' => 'Coach is not available for requested date and time',
                'data' => (object)null
            ];
        }

        //Book appoitment
        $bookAppoitment = new AppointmentBooking();
        $bookAppoitment->coach_id = $coach->id;
        $bookAppoitment->appointment_at = $dateTimeInRequiredTz;
        $bookAppoitment->appointment_until = date('Y-m-d H:i:s', strtotime($dateTimeInRequiredTz . "+ 30 minutes"));
        if ($bookAppoitment->save()) {
            $responseAppointmentAt = $this->converToTz($bookAppoitment->appointment_at, $requestdTimezone, $coach->timezone->name);
            $responseAppointmentUntil = $this->converToTz($bookAppoitment->appointment_until, $requestdTimezone, $coach->timezone->name);

            return [
                'status' => 1,
                'message' => 'Appointment booked successfully from ' . $responseAppointmentAt . ' to ' . $responseAppointmentUntil,
                'data' => (object)null
            ];
        }

        return [
            'status' => 1,
            'message' => 'Somethign went wrong',
            'data' => (object)null
        ];
    }
}
