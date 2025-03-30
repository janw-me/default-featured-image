<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Php54\Rector\Array_\LongArrayToShortArrayRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\EarlyReturn\Rector\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector;
use Rector\EarlyReturn\Rector\If_\ChangeIfElseValueAssignToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeNestedIfsToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\EarlyReturn\Rector\Return_\PreparedValueToEarlyReturnRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\EarlyReturn\Rector\StmtsAwareInterface\ReturnEarlyIfVariableRector;
use Rector\Php70\Rector\StmtsAwareInterface\IfIssetToCoalescingRector;

return RectorConfig::configure()
	->withPaths(
		array(
			__DIR__,
		)
	)
	->withSkipPath( __DIR__ . '/.github' )
	->withSkipPath( __DIR__ . '/languages' )
	->withSkipPath( __DIR__ . '/vendor' )

	->withPhpSets( php82: true )
	->withPreparedSets(
		deadCode: true,
	)
	->withImportNames(
		removeUnusedImports: true,
		importShortClasses: false,
	)
	->withRules(
		array(
			ChangeNestedForeachIfsToEarlyContinueRector::class,
			ChangeIfElseValueAssignToEarlyReturnRector::class,
			ChangeNestedIfsToEarlyReturnRector::class,
			RemoveAlwaysElseRector::class,
			PreparedValueToEarlyReturnRector::class,
			ReturnBinaryOrToEarlyReturnRector::class,
			ReturnEarlyIfVariableRector::class,
		)
	)
	->withSkip(
		array(
			LongArrayToShortArrayRector::class,
			TernaryToElvisRector::class,
			NullToStrictStringFuncCallArgRector::class, // PHPStan handles this better.
			FirstClassCallableRector::class,
			IfIssetToCoalescingRector::class,
		),
	);
