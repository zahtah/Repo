<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Allocation extends Model
{
    use HasFactory;
    protected $table = 'allocations';

    protected $fillable = [
        'row','Shahrestan','sal','erja','code','mantaghe','Abadi','kelace',
        'motaghasi','darkhast','Takhsis_group','masraf','comete','shomare',
        'date_shimare','vahed','q_m','V_m','t_mosavvab','sum','baghi','session','mosavabat',
        'file_name','file_category_id','minutes','status','created_by','approved_by'
    ];

    protected $casts = [
        'row' => 'integer',
        'sal' => 'integer',
        'code' => 'integer',
        'erja' => 'date',
        'comete' => 'date',
        'date_shimare' => 'date',
        'q_m' => 'integer',
        'V_m' => 'decimal:3',
        't_mosavvab' => 'decimal:3',
        'sum' => 'decimal:3',
        'baghi' => 'decimal:3',
    ];

    // مثال در Allocation model (به عنوان static)
    public static $map = [
        'رديف' => 'row',
        'شهرستان' => 'Shahrestan',
        'سال' => 'sal',
        'تاريخ ارجاع' => 'erja',
        'كد محدوده مطالعاتي' => 'code',
        'نوع منطقه' => 'mantaghe',
        'نام آبادي' => 'Abadi',
        'كلاسه پرونده' => 'kelace',
        'نام متقاضي' => 'motaghasi', 
        'نوع درخواست' => 'darkhast',
        'تخصيص' => 'Takhsis_group',
        'مصرف' => 'masraf',
        'تاريخ تشكيل كميته' => 'comete',
        'شماره مصوبه' => 'shomare',
        'تاريخ مصوبه' => 'date_shimare',
        'واحد دبی' => 'vahed',
        'مصوب دبی' => 'q_m',
        'حجم دبی' => 'V_m',
        'تخصيص پنجم' => 't_mosavvab',
        'جمع' => 'sum',
        'باقيمانده' => 'baghi',
        'مصوبات' => 'mosavabat',
    ];

    public function fileCategory()
    {
        return $this->belongsTo(FileCategory::class);
    }
    

    public function getErjaJalaliAttribute()
    {
        return $this->erja ? Jalalian::fromDateTime($this->erja)->format('Y/m/d') : null;
    }

    public function getCometeJalaliAttribute()
    {
        return $this->comete ? Jalalian::fromDateTime($this->comete)->format('Y/m/d') : null;
    }

    public function getDateShimareJalaliAttribute()
    {
        return $this->date_shimare ? Jalalian::fromDateTime($this->date_shimare)->format('Y/m/d') : null;
    }


    // در booted observer قبل از save یا creating/updating فراخوانی می‌کنیم
    protected static function booted()
    {
        // قبل از ایجاد
        static::creating(function ($allocation) {
            // محاسبه sum و baghi
            //$allocation->computeAndSetSum();

            // اگر میخواهی baghi هم ذخیره شود (t_mosavvab - sum)
            if (!isset($allocation->baghi)) {
                $t = $allocation->t_mosavvab ? (float)$allocation->t_mosavvab : 0.0;
                $s = $allocation->sum ? (float)$allocation->sum : 0.0;
                $allocation->baghi = round($t - $s, 3);
            }

            //مقداردهی خودکار file_category_id پس از ایمپورت اکسل

            if($allocation->file_category_id){
                return;
            }
            static $map=null;
            if ($map===null){
                $map=FileCategory::pluck('id','name')->toArray();
            }
            if (isset($map[$allocation->file_name])) {
                $allocation->file_category_id = $map[$allocation->file_name];
            }

        });

        // قبل از به‌روزرسانی
        static::updating(function ($allocation) {
            // محاسبه مجدد sum با توجه به مقادیر جدید (V_m, code, Takhsis_group ممکن است تغییر کرده باشند)
            //$allocation->computeAndSetSum();

            if (!isset($allocation->baghi)) {
                $t = $allocation->t_mosavvab ? (float)$allocation->t_mosavvab : 0.0;
                $s = $allocation->sum ? (float)$allocation->sum : 0.0;
                $allocation->baghi = round($t - $s, 3);
            }
        });
    }
    // Accessorها: وقتی از $allocation->V_m استفاده می‌کنیم، یک float بازگردانده شود
public function getV_mAttribute($value)
{
    if ($value === null || $value === '') return null;
    return (float) $value; // بازگرداندن عدد اعشاری برای محاسبات
}

public function getT_mosavvabAttribute($value)
{
    if ($value === null || $value === '') return null;
    return (float) $value;
}

public function getSumAttribute($value)
{
    if ($value === null || $value === '') return null;
    return (float) $value;
}

public function getBaghiAttribute($value)
{
    if ($value === null || $value === '') return null;
    return (float) $value;
}
public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function approver()
{
    return $this->belongsTo(User::class, 'approved_by');
}

}

    


