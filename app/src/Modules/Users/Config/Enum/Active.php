<?php

namespace App\Modules\Users\Config\Enum;

enum Active: string {
	case Yes = 'Yes';
	case No = 'No';
	case Pending = 'Pending';
}
