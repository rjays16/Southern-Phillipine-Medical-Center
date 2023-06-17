<?php

$config = array(
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.events.*',
        'application.vendors.*',
        'application.components.fis.*',
        'application.components.pest.*',
        'application.components.event.*',
        'application.modules.integrations.commands.*'
    ),    

    'commandMap' => array(
        /**
         * Code quality checking
         */
//        'grump' => array(
//            'class' => GrumpCommand::class
//        ),

        /**
         *
		*/
//        'integrations.run' => array(
//            'class' => 'SegHEIRS.modules.integrations.commands.RunIntegrations',
//        ),
        
        'runsocketserver' => array(
            'class' => 'SegHEIRS.modules.integrations.commands.RunSocketServerCommand',
	),
    )
);

return $config;