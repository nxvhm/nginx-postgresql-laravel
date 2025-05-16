<?php

namespace App\Modules\Users\Config\Enum;

enum TwoStepVerification: string {
	case Off = 'Off';
	case SMS = 'SMS';
	case Email = 'Email';
}
