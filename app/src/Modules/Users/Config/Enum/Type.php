<?php

namespace App\Modules\Users\Config\Enum;

enum Type: string {
	case Staff = 'Staff';
	case Agent = 'Agent';
	case Merchant = 'Merchant';
	case AppConnect = 'AppConnect';
}
