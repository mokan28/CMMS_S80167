<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Vessel,Asset,MaintenancePlan,SpareItem,User};
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $v = Vessel::create(['name'=>'Training Vessel Seri Ilmu','description'=>'Fictional training vessel']);

        $assets = [
            ['A01','Port main engine','Propulsion','Engine room','high'],
            ['A02','Starboard main engine','Propulsion','Engine room','high'],
            ['A03','Port gearbox','Transmission','Engine room','medium'],
            ['A04','Starboard gearbox','Transmission','Engine room','medium'],
            ['A05','Generator set','Electrical power','Engine room','high'],
            ['A06','Bilge pump','Bilge system','Bilge','medium'],
            ['A07','Fire pump','Fire main','Fire station','high'],
            ['A08','Steering hydraulic unit','Steering','Steering flat','high'],
            ['A09','Sea-water cooling pump','Cooling','Engine room','medium'],
            ['A10','Air compressor','Compressed air','Compressor room','low'],
        ];
        foreach ($assets as $a) {
            Asset::create(['vessel_id'=>$v->id,'code'=>$a[0],'name'=>$a[1],'subsystem'=>$a[2],'location'=>$a[3],'criticality'=>$a[4],'service_status'=>'in_service','has_meter'=>in_array($a[0],['A01','A02','A05'])]);
        }

        $plans = [
            // 3 calendar
            ['A01','Oil change 30d','calendar',30,null,'2026-11-01',null],
            ['A06','Bilge inspection 14d','calendar',14,null,'2026-11-15',null],
            ['A07','Fire pump test 7d','calendar',7,null,'2026-11-25',null],
            // 3 hours
            ['A01','Engine overhaul 250h','hours',null,250,null,1200],
            ['A05','Generator service 500h','hours',null,500,null,800],
            ['A09','Cooling pump 200h','hours',null,200,null,300],
            // 2 dual
            ['A02','Main engine annual','dual',30,250,'2026-11-01',1200],
            ['A08','Steering check','dual',14,100,'2026-11-20',400],
        ];
        foreach ($plans as $p) {
            $asset = Asset::where('code',$p[0])->first();
            MaintenancePlan::create([
                'asset_id'=>$asset->id,'title'=>$p[1],'trigger_type'=>$p[2],
                'interval_days'=>$p[3],'interval_hours'=>$p[4],
                'last_completion_date'=>$p[5],'last_completion_hours'=>$p[6],
            ]);
        }

        foreach ([
            ['CE Demo','ce@demo.test','chief_engineer'],
            ['Tech Demo','tech@demo.test','technician'],
            ['Viewer Demo','viewer@demo.test','viewer'],
        ] as $u) {
            User::create(['name'=>$u[0],'email'=>$u[1],'password'=>Hash::make('password'),'role'=>$u[2]]);
        }

        foreach ([
            ['SP01','Engine oil filter','pcs',10,3],
            ['SP02','Bilge pump seal','pcs',4,2],
            ['SP03','Fire hose nozzle','pcs',2,1],
            ['SP04','Coolant 20L','can',6,2],
            ['SP05','Air filter element','pcs',5,2],
            ['SP06','Hydraulic fluid 5L','can',3,1],
        ] as $s) {
            SpareItem::create(['code'=>$s[0],'description'=>$s[1],'unit'=>$s[2],'quantity'=>$s[3],'reorder_level'=>$s[4]]);
        }
    }
}
