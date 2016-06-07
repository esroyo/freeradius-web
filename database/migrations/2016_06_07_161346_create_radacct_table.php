<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRadacctTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('radacct', function($table) {
            $table->bigIncrements('radacctid');
			$table->string('acctsessionid', 64)->default('');
  			$table->string('acctuniqueid', 32)->default('');
  			$table->string('username', 64)->default('');
   			$table->string('groupname', 64)->default('');
   			$table->string('realm', 64)->default('')->nullable();
   			$table->string('nasipaddress', 15)->default('');
   			$table->string('nasportid', 15)->default(null)->nullable();
   			$table->string('nasporttype', 32)->default(null)->nullable();
   			$table->dateTime('acctstarttime')->default(null)->nullable();
			$table->dateTime('acctupdatetime')->default(null)->nullable();
			$table->dateTime('acctstoptime')->default(null)->nullable();
   			$table->integer('acctinterval')->default(null)->nullable();
   			$table->integer('acctsessiontime')->unsigned()->default(null)->nullable();
   			$table->string('acctauthentic', 32)->default(null)->nullable();
   			$table->string('connectinfo_start', 50)->default(null)->nullable();
   			$table->string('connectinfo_stop', 50)->default(null)->nullable();
   			$table->bigInteger('acctinputoctets')->default(null)->nullable();
   			$table->bigInteger('acctoutputoctets')->default(null)->nullable();
   			$table->string('calledstationid', 50)->default('');
   			$table->string('callingstationid', 50)->default('');
   			$table->string('acctterminatecause', 32)->default('');
   			$table->string('servicetype', 32)->default(null)->nullable();
   			$table->string('framedprotocol', 32)->default(null)->nullable();
   			$table->string('framedipaddress', 15)->default('');
			// $table->primary('radacctid');
			$table->unique('acctuniqueid');
  			$table->index('username');
  			$table->index('framedipaddress');
  			$table->index('acctsessionid');
  			$table->index('acctsessiontime');
  			$table->index('acctstarttime');
  			$table->index('acctinterval');
  			$table->index('acctstoptime');
  			$table->index('nasipaddress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::drop('radacct');
    }
}
