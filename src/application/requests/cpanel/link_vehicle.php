<?php

class REQ_Link_Vehicle extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(32, AUTH::OPOR, true);
        }

        $vehicle_id = data::post('Vehicle_ID');
        $driver_id = data::post('Driver_ID');
        $class = data::post('Class');
        $model = data::post('Model');
        $color = strtolower(data::post('Color'));
        $plate = data::post('Plate');

        $veh = 'new-vehicle';
        if ($vehicle_id != 0) {

            $veh = db::get_r('vehicles', ['Class', 'Model', 'Color', 'Plate'], ['Vehicle_ID' => $vehicle_id])[0];
            $veh = hash('SHA1', $veh['Class'] . $veh['Model'] . $veh['Color'] . $veh['Plate']);
            if ($veh == hash('SHA1', $class . $model . $color . $plate)) {
                data::session('is', true);
                data::session('msg', 'بيانات سيارة الكابتن المرسلة مطابقة للبيانات المسجلة بالنظام! لم يحدث تسجيل لسيارة جديدة.');
                dirto(cd('cpanel/user/' . $driver_id));
            } else {
                db::set('vehicles', ['IsLinked' => 0], ['Vehicle_ID' => $vehicle_id]);
                if (!db::so()) {
                    data::session('is', false);
                    dirto(cd('cpanel/user/' . $driver_id));
                }
                $veh = 'changed-vehicle';
            }
        }

        db::put('vehicles', [
            'Driver_ID' => $driver_id,
            'IsLinked' => 1,
            'Class' => $class,
            'Model' => $model,
            'Color' => $color,
            'Plate' => $plate,
            'Registration' => date('Y-m-d H:i:s')
        ]);

        if (db::so()) {
            data::session('is', true);

            if ($veh == 'new-vehicle') {
                data::session('msg', 'تم تسجيل البيانات كسيارة كابتن جديدة بالنظام.');
            } else {
                data::session('msg', 'تم استبدال بيانات سيارة الكابتن القديمة بالنظام ببيانات السيارة الجديدة.');
            }
        } else {
            data::session('is', false);
        }

        dirto(cd('cpanel/user/' . $driver_id));
    }
}
