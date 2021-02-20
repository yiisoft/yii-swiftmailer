<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Mailer\FileMailer;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\MessageBodyRenderer;
use Yiisoft\Mailer\MessageBodyTemplate;
use Yiisoft\Mailer\MessageFactory;
use Yiisoft\Mailer\MessageFactoryInterface;
use Yiisoft\Mailer\SwiftMailer\Mailer;
use Yiisoft\Mailer\SwiftMailer\Message;
use Yiisoft\View\WebView;

/** @var array $params */

return [
    MessageBodyRenderer::class => [
        '__class' => MessageBodyRenderer::class,
        '__construct()' => [
            Reference::to(WebView::class),
            static fn (Aliases $aliases) => new MessageBodyTemplate(
                $aliases->get($params['yiisoft/mailer']['messageBodyTemplate']['viewPath']),
            ),
        ],
    ],

    MessageFactoryInterface::class => [
        '__class' => MessageFactory::class,
        '__construct()' => [
            Message::class,
        ],
    ],

    Swift_SmtpTransport::class => [
        '__class' => Swift_SmtpTransport::class,
        '__construct()' => [
            $params['swiftmailer/swiftmailer']['SwiftSmtpTransport']['host'],
            $params['swiftmailer/swiftmailer']['SwiftSmtpTransport']['port'],
            $params['swiftmailer/swiftmailer']['SwiftSmtpTransport']['encryption'],
        ],
        'setUsername()' => [$params['swiftmailer/swiftmailer']['SwiftSmtpTransport']['username']],
        'setPassword()' => [$params['swiftmailer/swiftmailer']['SwiftSmtpTransport']['password']],
    ],

    Swift_Transport::class => $params['yiisoft/mailer']['useSendmail']
        ? Swift_SendmailTransport::class : Swift_SmtpTransport::class,

    FileMailer::class => [
        '__class' => FileMailer::class,
        '__construct()' => [
            'path' => fn (Aliases $aliases) => $aliases->get(
                $params['yiisoft/mailer']['fileMailer']['fileMailerStorage']
            ),
        ],
    ],

    MailerInterface::class => $params['yiisoft/mailer']['writeToFiles']
        ? FileMailer::class : Mailer::class,
];
