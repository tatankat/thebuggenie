<?php

    $tbg_response->setTitle(__('Configuration center'));
    
?>
<div class="configuration_container">
    <div class="configuration_update_check_container">
        <a class="button button-silver" id="update_button" href="javascript:void(0);" onclick="TBG.Config.updateCheck('<?php echo make_url('configure_update_check'); ?>');"><?php echo __('Check for updates now'); ?></a>
        <?php echo image_tag('spinning_16.gif', array('id' => 'update_spinner', 'style' => 'display: none;')); ?>
        <?php echo __('You currently have version %thebuggenie_version of The Bug Genie.', array('%thebuggenie_version' => \thebuggenie\core\framework\Settings::getVersion())); ?>
    </div>
    <?php if (count($outdated_modules) > 0): ?>
        <div class="update_div rounded_box yellow" style="margin-top: 20px;">
            <div class="header"><?php echo __('You have %count outdated modules. They have been disabled until you upgrade them, you can upgrade them from Module settings.', array('%count' => count($outdated_modules))); ?></div>
        </div>
    <?php endif; ?>
    <h1><?php echo __('General configuration'); ?></h1>
    <ul class="config_badges">
    <?php foreach ($config_sections['general'] as $section => $info): ?>
        <li class="rounded_box">
        <?php if (is_array($info['route'])): ?>
            <?php $url = make_url($info['route'][0], $info['route'][1]); ?>
        <?php else: ?>
            <?php $url = make_url($info['route']); ?>
        <?php endif; ?>
            <a href="<?php echo $url; ?>">
                <b>
                    <?php if (isset($info['fa_icon'])): ?>
                        <?php echo fa_image_tag($info['fa_icon'], [], $info['fa_style']); ?>
                    <?php else: ?>
                        <?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
                    <?php endif; ?>
                    <?php echo $info['description']; ?>
                </b>
                <span><?php echo $info['details']; ?></span>
            </a>
        </li>
    <?php endforeach; ?>
    <?php foreach ($config_sections[\thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_MODULES] as $section => $info): ?>
        <?php if ($info['module'] != 'core' && !\thebuggenie\core\framework\Context::getModule($info['module'])->hasConfigSettings()) continue; ?>
        <li class="rounded_box">
        <?php if (is_array($info['route'])): ?>
            <?php $url = make_url($info['route'][0], $info['route'][1]); ?>
        <?php else: ?>
            <?php $url = make_url($info['route']); ?>
        <?php endif; ?>
            <a href="<?php echo $url; ?>">
                <b>
                    <?php if (isset($info['fa_icon'])): ?>
                        <?php $style = (isset($info['fa_color'])) ? 'color: ' . $info['fa_color'] : ''; ?>
                        <?= fa_image_tag($info['fa_icon'], ['style' => $style], $info['fa_style']); ?>
                    <?php elseif ($info['module'] != 'core'): ?>
                        <?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;'), false, $info['module']); ?>
                    <?php else: ?>
                        <?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
                    <?php endif; ?>
                    <?php echo $info['description']; ?>
                </b>
                <span><?php echo $info['details']; ?></span>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
    <br style="clear: both;">
</div>
