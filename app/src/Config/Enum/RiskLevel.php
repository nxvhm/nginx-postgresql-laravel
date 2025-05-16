<?php

namespace App\Config\Enum;

enum RiskLevel: string {

	case LOW_ELIGIBLE = 'LowEligible';
	case LOW_NON_ELIGIBLE = 'LowNonEligible';
	case MODERATE_ELIGIBLE = 'ModerateEligible';
	case MODERATE_NON_ELIGIBLE = 'ModerateNonEligible';
	case HIGH_ELIGIBLE = 'HighEligible';
	case HIGH_NON_ELIGIBLE = 'HighNonEligible';

}
