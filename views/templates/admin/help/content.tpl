{**
 * Copyright Younited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @copyright Younited
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 *}
<div class="younitedpayApp mt-4 pt-4">
  <div class="row">
    <div class="col-lg-12">
        <section class="accordion-section clearfix" aria-label="Question Accordions">
            <div class="container">
                <div class="panel-group pt-2" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default mb-3">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading0" data-target="#collapse0">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse0" aria-expanded="true" aria-controls="collapse0">
                                    Quelle est la configuration n&eacute;cessaire pour utiliser le module de paiement YounitedPay ?
                                </a>
                            </h4>
                        </div>
                        <div id="collapse0" class="collapse" role="tabpanel" aria-labelledby="heading0">
                            <div class="panel-body px-3 mb-4">
                                CURL (requ&egrave;te l'API)<br />
                                SSL & TLS v1.2 (sécurité HTTPS)<br />
                                Configurer les WebHooks pour le status des paiements acceptés
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading1" data-target="#collapse1">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    Les status de paiement ne sont pas mis à jour, quelle peut &egrave;tre la cause ?
                                </a>
                            </h4>
                        </div>
                        <div id="collapse1" class="collapse" role="tabpanel" aria-labelledby="heading1">
                            <div class="panel-body px-3 mb-4">
                                <ul>
                                    <li>
                                        V&eacute;rifiez que vous avez bien configur&eacute; le WebHook avec votre boutique.
                                    </li>
                                    <li>
                                        Regarder les réponses de la boutique (DashBoard => WebHooks => R&eacute;ponses)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading2" data-target="#collapse2">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse2" aria-expanded="true">
                                    Comment contacter notre support technique ?
                                </a>
                            </h4>
                        </div>
                        <div id="collapse2" class="collapse" role="tabpanel">
                            <div class="panel-body px-3 mb-4">
                                <ul>
                                    <li>
                                        Notre centre d'aide : 
                                        <a href="https://www.younited-pay.com/#contactModalFR" 
                                            target="_blank"
                                            title="Lien vers le centre d'aide">
                                            Cliquez ici
                                        </a>
                                    </li>
                                    <li>
                                        Regarder les réponses de la boutique (DashBoard => WebHooks => R&eacute;ponses)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default mb-3">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading3" data-target="#collapse3">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse3" aria-expanded="true">
                                    Que faire en cas d'anomalie de paiement / status ?
                                </a>
                            </h4>
                        </div>
                        <div id="collapse3" class="collapse" role="tabpanel" aria-labelledby="heading3">
                            <div class="panel-body px-3 mb-4">
                                <ul>
                                    <li>
                                        Consulter les logs
                                    </li>
                                    <li>
                                        Notre centre d'aide : 
                                        <a href="https://www.younited-group.com/about-us/" 
                                            target="_blank"
                                            title="Lien vers le centre d'aide">
                                            Cliquez ici
                                        </a>
                                    </li>
                                    <li>
                                        N'hésitez pas à contacter notre support technique si vous n&apos;avez pas la solution.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
  </div>
</div>
