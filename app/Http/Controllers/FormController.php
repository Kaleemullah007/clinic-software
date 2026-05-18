<?php

namespace App\Http\Controllers;

use App\Mail\GenericForm;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\User;
use App\Rules\ValidReCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class FormController extends Controller
{
    public function store()
    {

        $formData = request()->all();
        $email_config = config('mubashir' . '.email_forms.' . $formData['cp_form_name']);
        $page_message = $email_config['message']??'Thanks for contacting us';
        // Validate form data if we have validation rules
        $validation_rules = [];
        $validation_messages = [];

        // All of this mess is parsing the validation rules in the config file for the form.
        if (! empty($email_config['validation'])) {
            $validation_messages = ! empty($email_config['validation']['messages']) ? $email_config['validation']['messages'] : [];
            $validation_collection = collect($email_config['validation']['rules']);
            $validation_collection->transform(function ($item, $field) {
                if (! is_array($item)) {
                    return $item;
                }
                $ret_item = [];
                foreach ($item as $key => $value) {
                    if ($key !== 'Rule') {
                        $ret_item[] = $value;
                    } else {
                        foreach ($value as $rule_type => $rule_values) {
                            switch ($rule_type) {
                                case 'in':
                                    $ret_item[] = Rule::in($rule_values);
                                    break;
                                case 'notIn':
                                    $ret_item[] = Rule::notIn($rule_values);
                                    break;
                                case 'recaptcha':
                                    $ret_item[] = new ValidReCaptcha();
                                    break;
                            }
                        }
                    }
                }
                return $ret_item;
            });
            $validation_rules = $validation_collection->toArray();
        }

        $data = request()->validate($validation_rules, $validation_messages);

        // If we get here, the form data validation passed.

        // Clean form data of behind the scenes data the exhibitor doesn't care about.
        $config_type  = $formData['cp_form_name'];
        unset($formData['_token']);
        unset($formData['cp_form_name']);
        unset($formData['g-recaptcha-response']);
        unset($formData['submit']);
        // Remove submit button

        foreach ($email_config['mailto'] as $mailto) {
            Mail::to($mailto)->send(new GenericForm($email_config, $formData));
        }

        $fragment = '';
        unset($data['txtSpamCode']);
        if($config_type == 'appointment'){
            unset($data['service']);

            $data['service_id'] = 1;



            $user = [
                'name' => $data['name'],
                'role' => 'patient',
                'email'=>$data['email'],
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ];
            $user = User::create($user);

            $data['user_id'] = $user->id;
            $data['is_paid'] = 'pending';
            $data['appointment_status'] = 'pending';
        $fragment = 'MakeAppointment';
        Appointment::create($data);
        }
        elseif($config_type == 'contact_us'){
            $fragment = 'contact';
            // dd($data);
            Contact::Create($data);

        }




        // $url = redirect()->back()->with('message',config('mubashir.message'));
        // if(!empty($fragment))
        // $url->withFragment($fragment);
        // return $url;

        return redirect()->back()->with('message',$page_message)
        ->withFragment($fragment);
    }
}
