<?php

namespace App\Imports;

use App\Notifications\TransactionNotification;
use App\Notifications\WelcomeWithPasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToCollection, WithHeadingRow, SkipsOnFailure, WithValidation, WithChunkReading
{
    use Importable, SkipsFailures;
    private $mail   = 0;
    private $errors = [];
    public function __construct(int $mail)
    {
        $this->mail = $mail;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        $rows      = $rows->toArray();
        $rownumber = 1;
        $rownumber = $rownumber + 1;
        foreach ($rows as $key => $row) {
            $validator = Validator::make($row, $this->rules());
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $messages) {
                    foreach ($messages as $error) {
                        if (array_key_exists($rownumber, $this->errors)) {
                            array_push($this->errors[$rownumber], $error);
                        } else {
                            $this->errors[$rownumber] = [$error];
                        }
                    }
                }
            } else {
                $password = trim($row['password']);
                $user     = User::create([
                    'name'              => $row['name'],
                    'email'             => strtolower($row['email']),
                    'email_verified_at' => Carbon::now(),
                    'password'          => Hash::make($password),
                    'mobile_number'     => $row['mobile_no'],
                    'dob'               => ($row['date_of_birth'] != '') ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_of_birth'])) : null,
                    'created_at'        => ($row['date_joined'] != '') ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_joined'])) : Carbon::now(),
                    'membership_type'   => (strtolower($row['membership_tier_purple_gold']) == 'gold') ? 2 : 1,
                    'gender'            => (strtolower($row['gender']) == 'male') ? 1 : ((strtolower($row['gender']) == 'female') ? 2 : 0),
                    'account_status'    => ($row['status_active_non_active'] != 'Active') ? 0 : 1,
                ]);
                $user->assignRole('user');
                if (!empty($row['add_points']) && $row['add_points'] > 0) {
                    $transaction = $user->creditPoints([
                        'transaction_value' => $row['add_points'],
                        'data'              => json_encode(['message' => "Points Added", 'sub_heading' => 'By Project Acai Admin']),
                    ]);
                    $user->notify(new TransactionNotification($transaction));
                }
                $passwordtoken = app('auth.password.broker')->createToken($user);
                if ($this->mail == 1) {
                    //$user->notify(new WelcomeWithPasswordReset($user->name, $user->email, $password, $passwordtoken));
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'name'      => 'required',
            'email'     => 'required|unique:users,email',
            'mobile_no' => 'required|unique:users,mobile_number',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
