<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// hook the decode
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc']['tx_spurl'] = 'EXT:spurl/Classes/Domain/Utility/HookHandler.php:&\Rattazonk\Spurl\Domain\Utility\HookHandler->decode';

// hook the typoscript link creation
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['linkData-PostProc']['tx_spurl'] = 'EXT:spurl/Classes/Domain/Utility/HookHandler.php:&\Rattazonk\Spurl\Domain\Utility\HookHandler->hookTypoScriptLinkCreation';
// hook the content link creation (rte ...)
// $TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['typoLink_PostProc']['tx_spurl'] = 'EXT:spurl/class.tx_realurl.php:&tx_realurl->encodeSpURL_urlPrepend';
?>