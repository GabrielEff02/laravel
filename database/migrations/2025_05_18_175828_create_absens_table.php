<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensTable extends Migration
{
    public function up()
    {
        Schema::create('absen', function (Blueprint $table) {
            $table->id('NO_ID');
            $table->string('KODEP', 30);
            $table->string('NAMAP', 40)->nullable();
            $table->date('TGL')->default('2001-01-01');
            $table->time('WAKTU')->default('00:00:00');
            $table->string('FLAG', 30)->nullable();
            $table->string('KD_GRUP', 20)->nullable();
            $table->string('NO_BUKTI', 30)->nullable();
            $table->string('PER', 10)->nullable();
            $table->string('URAIAN', 30)->nullable();
            $table->string('NA_GRUP', 40)->nullable();
            $table->string('DR', 10)->nullable();
            $table->string('NOTES', 200)->nullable();
            $table->decimal('TPREMI_HR', 17, 2)->default(0);
            $table->decimal('THARIAN', 17, 2)->default(0);
            $table->decimal('T_HR', 17, 2)->default(0);
            $table->decimal('TJAM1', 17, 2)->default(0);
            $table->decimal('TJAM2', 17, 2)->default(0);
            $table->decimal('TJAM1RP', 17, 2)->default(0);
            $table->decimal('TJAM2RP', 17, 2)->default(0);
            $table->decimal('TLAIN', 17, 2)->default(0);
            $table->decimal('TINSENTIF', 17, 2)->default(0);
            $table->decimal('TJUMLAH', 17, 2)->default(0);
            $table->dateTime('TG_SMP')->default('2001-01-01 00:00:00');
            $table->string('USRNM', 50)->nullable();
            $table->dateTime('i_tgl')->default('2001-01-01 00:00:01');
            $table->string('e_pc', 25)->nullable();
            $table->dateTime('e_tgl')->default('2001-01-01 00:00:01');
            $table->decimal('TPL', 17, 2)->default(0);
            $table->decimal('TMS', 17, 2)->default(0);
            $table->decimal('TIK', 17, 2)->default(0);
            $table->decimal('TNB', 17, 2)->default(0);
            $table->decimal('TGAJI', 17, 2)->default(0);
            $table->decimal('TBON', 17, 2)->default(0);
            $table->decimal('TSUBSIDI', 17, 2)->default(0);
            $table->decimal('TNETT', 17, 2)->default(0);
            $table->decimal('TPREMI', 17, 2)->default(0);
            $table->string('FASE', 5)->nullable();
            $table->string('PREMI', 1)->nullable();
            $table->decimal('TOT_POT', 17, 2)->default(0);
            $table->decimal('KIK_NETT', 17, 2)->default(0);
            $table->decimal('TBON1', 17, 2)->default(0);
            $table->decimal('TOT_KIK', 17, 2)->default(0);
            $table->decimal('OTHER', 17, 2)->default(0);
            $table->decimal('TJAM1THL', 17, 2)->default(0);
            $table->decimal('TJAM2THL', 17, 2)->default(0);
            $table->decimal('TJAM1RPHL', 17, 2)->default(0);
            $table->decimal('THAM2RPHL', 17, 2)->default(0);
            $table->decimal('TMSD', 17, 2)->default(0);
            $table->decimal('THR', 17, 2)->default(0);
            $table->decimal('TOTAL', 17, 2)->default(0);
            $table->decimal('TPOTONG', 17, 2)->default(0);
            $table->decimal('TSUBS', 17, 2)->default(0);
            $table->decimal('TTOT_HR', 17, 2)->default(0);
            $table->decimal('NETTO', 17, 2)->default(0);
            $table->decimal('TOT_BON', 17, 2)->default(0);
            $table->decimal('LAIN', 17, 2)->default(0);
            $table->tinyInteger('PT')->default(0);
            $table->string('CV', 5)->nullable();
            $table->string('ACNO', 17)->nullable();
            $table->string('created_by', 20)->nullable();
            $table->dateTime('created_at')->default('2001-01-01 00:00:00');
            $table->string('updated_by', 20)->nullable();
            $table->dateTime('updated_at')->default('2001-01-01 00:00:00');
            $table->tinyInteger('POSTED')->default(0);
            $table->decimal('XAA', 17, 2)->default(0);
            $table->decimal('XBB', 17, 2)->default(0);
            $table->decimal('XCC', 17, 2)->default(0);
            $table->decimal('XDD', 17, 2)->default(0);
            $table->decimal('XEE', 17, 2)->default(0);
            $table->decimal('UMR', 17, 2)->default(0);
            $table->decimal('TK1', 17, 2)->default(0);
            $table->decimal('TK2', 17, 2)->default(0);
            $table->decimal('TKELILING', 17, 2)->default(0);
            $table->decimal('TOVERTIME', 17, 2)->default(0);
            $table->time('jam_in')->default('00:00:00');
            $table->time('jam_out')->default('00:00:00');
            $table->string('image_in', 100)->nullable();
            $table->string('image_out', 100)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absens');
    }
}
