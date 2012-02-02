<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_cache::initializeCachingFramework();

if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magic_cache'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magic_cache'] = array();
}

if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magic_cache']['frontend'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magic_cache']['frontend'] = 't3lib_cache_frontend_VariableFrontend';
}

if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magic_cache']['backend'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magic_cache']['backend'] = 't3lib_cache_backend_DbBackend';
}

if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magic_cache']['options'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magic_cache']['options'] = array();
}

?>