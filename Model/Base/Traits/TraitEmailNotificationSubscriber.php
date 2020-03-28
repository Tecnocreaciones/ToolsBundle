<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Base\Traits;

/**
 * Funciones comunes para listerner de correos
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
trait TraitEmailNotificationSubscriber
{
    private $twigSwiftMailer;
    
    
    /**
     * Enviar un correo
     * @param type $id
     * @param array $recipient
     * @param array $context
     * @return type
     */
    private function sendEmailDocument($id, $recipient, array $context = array(), array $attachs = [])
    {
        $emailService = $this->getEmailService();
        $messageSend = $emailService->sendDocumentMessage($id, $context, $recipient, $attachs);
        return $messageSend;
    }

    /**
     * Prepara un correo para generarlo y enviarlo posteriormente
     * @param type $id
     * @param type $recipient
     * @param array $context
     * @param array $attachs
     * @return type
     */
    private function emailQueue($id, $recipient, array $context = array(), array $attachs = [], array $extras = [])
    {
        $emailService = $this->getEmailService();
        $messageSend = $emailService->emailQueue($id, $context, $recipient, $attachs, $extras);
        return $messageSend;
    }

    /**
     * Servicio para enviar email
     * @return \Tecnoready\Common\Service\Email\TwigSwiftMailer
     */
    private function getEmailService()
    {
        return $this->twigSwiftMailer;
    }

    /**
     * Servicio para enviar email
     * @return \Tecnoready\Common\Service\Email\TwigSwiftMailer
     * @required
     */
    public function setEmailService(\Tecnoready\Common\Service\Email\TwigSwiftMailer $twigSwiftMailer)
    {
        $this->twigSwiftMailer = $twigSwiftMailer;
    }
}
