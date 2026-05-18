<?php
namespace App\Enums;

enum RoleName:string
{

    case ADMIN = 'admin';
    case DOCTOR = 'doctor';
    case FRONTDESK = 'frontDesk';
    case PATIENT = 'patient';
    case TESTER = 'tester';
    case DEVELOPER = 'developer';
}
