<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/15/2018
 * Time: 7:11 PM
 */


// The following block would be put at the level just below the mode in a scribe config for entity
$config = [
    'notifications'=>[ // A list of arbitrary key names with the actual notifications information that will be sent
        '<string>'=> [ // Make null to disable this notification
            'enabled'=>'<boolean|null>', // Defaults to true. This is where or not notifications are enabled for this context and mode.
            'notification'=>'<Notification>', // A notification to send. If you use a notification that extends RavenGeneralNotification the rest of the functionality that can be set in this config will be used
            'settings'=>[ // Optional. Can be null to disable the block.
                'closure'=>'<closure|null>', // Optional. A closure to test if this should be sent in this via or not
            ],
            'via'=>[ // A key name for the type of notification, such as: mail, nexmo, etc. A value of an array with additional properties.
                'mail'=>[ // Optional. Sends a notification via email and RavenGeneralNotification's, or extended classes there of can use the settings here automatically. Note that view logic is still left to the notification class
                    'enabled'=>'<boolean|null>', // Defaults to true. This is whether or not the via is enabled.
                    'to'=>'<string|null>', // A email address to send too. Remember that you can use a closure here to get the email address off the entity (as can be done with all Scribe configs).
                    'replyTo'=>'<string|null>', // Defaults to env DEFAULT_REPLY_TO_EMAIL. The email address to reply too.
                    'cc'=>'<string[]|null>', // An array of people to cc the email too.
                    'from'=>'<string|null>', // Defaults to env DEFAULT_FROM_EMAIL. The email address the email is from.
                    'priority'=>'<number|null>',// A priority for the email
                    'settings'=>[ // Optional. Can be null to disable the block.
                        'closure'=>'<closure|null>', // Optional. A closure to test if this should be sent in this via or not
                    ],
                    'options'=>'<array|null>' // Optional. Any additional user options you would like to have passed to your notification
                ],
                'nexmo'=>[ // Optional. Sends notifications via nexmo. Note that view logic is still isolated to the notification class
                    'enabled'=>'<boolean|null>', // Defaults to true. This is whether or not the via is enabled.
                    'from'=>'<string|null>', // Defaults to env DEFAULT_FROM_PHONE. A number to send the text message from.
                    'to'=>'<string|null>', // A number to send the text message to.
                    'settings'=>[ // Optional. Can be null to disable the block.
                        'closure'=>'<closure|null>', // Optional. A closure to test if this should be sent in this via or not
                    ],
                    'options'=>'<array|null>' // Any additional user options you would like to have passed to your notification
                ],
                'slack'=>[ // Optional. Sends notifications via slack.
                    'enabled'=>'<boolean|null>', // Defaults to true. This is whether or not the via is enabled.
                    'from'=>'<string|null>', // Defaults to env DEFAULT_FROM_SLACK. A slack user to send from
                    'to'=>'<string|null>', // channel to send to
                    'settings'=>[ // Optional. Can be null to disable the block.
                        'closure'=>'<closure|null>', // Optional. A closure to test if this should be sent in this via or not
                    ],
                ],
                '<DoctrineChannel::class|database>'=>[ // Optional. Additional methods of sending notifications which Raven has no additional options for
                    'enabled'=>'<boolean|null>', // Defaults to true. This is whether or not the via is enabled.
                    'settings'=>[ // Optional. Can be null to disable the block.
                        'closure'=>'<closure|null>', // Optional. A closure to test if this should be sent in this via or not
                    ],
                    'options'=>'<array|null>' // Optional. Any additional user options you would like to have passed to your notification
                ],
                'broadcast'=>[ // Optional. Sends notification as a broadcast. Please note that unlike default notifications
                    'enabled'=>'<boolean|null>', // Defaults to true. This is whether or not the via is enabled.
                    'channel'=>'<string|null>', // A channel to send the notification on. Works with Laravel 5.6
                    'settings'=>[ // Optional. Can be null to disable the block.
                        'closure'=>'<closure|null>', // Optional. A closure to test if this should be sent in this via or not
                    ],
                    'options'=>'<array|null>' // Optional. Any additional user options you would like to have passed to your notification
                ]
            ]
        ]
    ],
];