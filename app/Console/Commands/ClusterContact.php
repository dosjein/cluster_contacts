<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use FullContact;
use App\Models\Persons;

class ClusterContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fullcontact:recheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check FullContact details for contacts in ClusterPoint';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Recheck FullContacts by ClusterPoint model');

        $this->info('undefined');
        $clusterPersons = $this->performExtract(Persons::where('typeof(fullcontact)' , 'undefined')
                            ->limit(100));

        $this->info('202');
        $clusterPersons = $this->performExtract(Persons::where('CONTAINS("status:202")')
                            ->limit(100));
        $this->info('404');
        $clusterPersons = $this->performExtract(Persons::where('CONTAINS("status:404")')
                            ->limit(100));
    }

    private function performExtract($personQuery){
       foreach ($personQuery->get() as $person) {
            $fullDataArray = FullContact::lookupByEmail($person->email);

            $personModelObject = Persons::where('_id' , $person->_id)->first();

            if ($personModelObject->_id){
                $personModelObject->fullcontact = (object) $fullDataArray;
                $this->info((is_object($personModelObject->fullcontact) ? $personModelObject->fullcontact->status : '000').':'.$person->email);
                $personModelObject->save();
                sleep(1);
            }else{
                $this->error('not ok that not exists '.$person->_id.' ( '.$person->email.')');
                sleep(2);
            }

            sleep(2);
       }  
    }
}
