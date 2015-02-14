<?php 
class TIG_Afterpay_Block_RejectMessages extends Mage_Core_Block_Abstract
{
    protected $_rejectTemplate;
    
    public $template1 = "
    	<p>
        	Het spijt ons u te moeten mededelen dat uw aanvraag om uw bestelling achteraf te betalen op dit moment niet door AfterPay wordt geaccepteerd. 
        	Dit kan om diverse (tijdelijke) redenen zijn. 
    	</p>
    	<p>
    		Voor vragen over uw afwijzing kunt u contact opnemen met de <a href=\"http://www.afterpay.nl/consument-contact\" target=\"_blank\">Klantenservice van AfterPay</a>. 
    		Of kijk op de website van AfterPay bij 'Veelgestelde vragen' via de link <a href=\"http://www.afterpay.nl/page/consument-faq\" target=\"_blank\">http://www.afterpay.nl/page/consument-faq</a> onder het kopje \"Gegevenscontrole\". 
    	</p>
    	<p>
    		Wij adviseren u voor een andere betaalmethode te kiezen om alsnog de betaling van uw bestelling af te ronden.
    	</p>
    ";
    
    public $template29 = "
    	<p>
        	Hartelijk welkom bij AfterPay. 
        	Het spijt ons u te moeten mededelen dat uw aanvraag om uw bestelling achteraf te betalen op dit moment niet door AfterPay wordt geaccepteerd. 
        	AfterPay hanteert voor voor eerste gebruikers een instapbedrag. 
        	Uw huidige orderbedrag overstijgt het instapbedrag. 
    	</p>
    	<p>
    		Voor vragen over uw afwijzing kunt u contact opnemen met de <a href=\"http://www.afterpay.nl/consument-contact\" target=\"_blank\">Klantenservice van AfterPay</a>. 
    		Of kijk op de website van AfterPay bij 'Veelgestelde vragen' via de link <a href=\"http://www.afterpay.nl/page/consument-faq\" target=\"_blank\">http://www.afterpay.nl/page/consument-faq</a> onder het kopje \"Gegevenscontrole\". 
    	</p>
    	<p>
    		Wij adviseren u voor een andere betaalmethode te kiezen om alsnog de betaling van uw bestelling af te ronden.
    	</p>
    ";
    
    public $template30 = "
    	<p>
        	Hartelijk welkom bij AfterPay. 
        	Het spijt ons u te moeten mededelen dat uw aanvraag om uw bestelling achteraf te betalen op dit moment niet door AfterPay wordt geaccepteerd. 
        	Graag wil AfterPay uw betaalverzoek accepteren, echter volgens onze administratie heeft u het maximale aantal openstaande betalingen bereikt. 
        	Indien u tot betaling overgaat zijn wij u graag weer snel van dienst.
    	</p>
    	<p>
    		Voor vragen over uw afwijzing kunt u contact opnemen met de <a href=\"http://www.afterpay.nl/consument-contact\" target=\"_blank\">Klantenservice van AfterPay</a>. 
    		Of kijk op de website van AfterPay bij 'Veelgestelde vragen' via de link <a href=\"http://www.afterpay.nl/page/consument-faq\" target=\"_blank\">http://www.afterpay.nl/page/consument-faq</a> onder het kopje \"Gegevenscontrole\". 
    	</p>
    	<p>
    		Wij adviseren u voor een andere betaalmethode te kiezen om alsnog de betaling van uw bestelling af te ronden.
    	</p>
    ";
    
    public $template36 = "
    	<p>
        	Het spijt ons u te moeten mededelen dat uw aanvraag om uw bestelling achteraf te betalen op dit moment niet door AfterPay wordt geaccepteerd. 
        	Helaas is het opgegeven e-mailadres volgens onze bronnen niet volledig of betsaat het niet. 
        	Indien u van AfterPay gebruik wilt maken, dient u gebruik te maken van een geldig en actief e-mailadres.
    	</p>
    	<p>
    		Voor vragen over uw afwijzing kunt u contact opnemen met de <a href=\"http://www.afterpay.nl/consument-contact\" target=\"_blank\">Klantenservice van AfterPay</a>. 
    		Of kijk op de website van AfterPay bij 'Veelgestelde vragen' via de link <a href=\"http://www.afterpay.nl/page/consument-faq\" target=\"_blank\">http://www.afterpay.nl/page/consument-faq</a> onder het kopje \"Gegevenscontrole\". 
    	</p>
    	<p>
    		Wij adviseren u om een geldig en actief e-mailadres te gebruiken bij uw bestelling.
    	</p>
    ";
    
    public $template40 = "
    	<p>
        	Het spijt ons u te moeten mededelen dat uw aanvraag om uw bestelling achteraf te betalen op dit moment niet door AfterPay wordt geaccepteerd. 
        	Helaas is uw leeftijd onder de 18 jaar. 
        	Indien u gebruik wilt maken van AfterPay dient uw leeftijd minimaal 18 jaar of ouder te zijn.
    	</p>
    	<p>
    		Wij adviseren u voor een andere betaalmethode te kiezen om alsnog de betaling van uw bestelling af te ronden.
    	</p>
    ";
    
    public $template42 = "
    	<p>
        	Het spijt ons u te moeten mededelen dat uw aanvraag om uw bestelling achteraf te betalen op dit moment niet door AfterPay wordt geaccepteerd. 
        	Helaas is uw adres informatie niet correct of niet compleet. 
        	Indien u van AfterPay gebruik wilt maken, dient het opgegeven adres een geldig woon/verblijf plaats te zijn.
    	</p>
    	<p>
    		Voor vragen over uw afwijzing kunt u contact opnemen met de <a href=\"http://www.afterpay.nl/consument-contact\" target=\"_blank\">Klantenservice van AfterPay</a>. 
    		Of kijk op de website van AfterPay bij 'Veelgestelde vragen' via de link <a href=\"http://www.afterpay.nl/page/consument-faq\" target=\"_blank\">http://www.afterpay.nl/page/consument-faq</a> onder het kopje \"Gegevenscontrole\". 
    	</p>
    	<p>
    		Wij adviseren u om een correct woon/verblijf plaats in te vullen bij uw bestelling.
    	</p>
    ";
    
    public $template71 = "
    	<p>
        	Hartelijk welkom bij AfterPay. 
        	Het spijt ons u te moeten mededelen dat uw aanvraag om uw bestelling achteraf te betalen op dit moment niet door AfterPay wordt geaccepteerd. 
        	helaas kunnen wij uw kamer van koophandel dossier niet raadplegen. 
        	Dit kan als oorzaak hebben dat uw KVK nummer niet juist is en/of de bedrijfsnaam die u ingegegeven heeft niet overeenkomt met hetgeen geregistreerd staat bij de kamer van koophandel.
    	</p>
    	<p>
    		Voor vragen over uw afwijzing kunt u contact opnemen met de <a href=\"http://www.afterpay.nl/consument-contact\" target=\"_blank\">Klantenservice van AfterPay</a>. 
    		Of kijk op de website van AfterPay bij 'Veelgestelde vragen' via de link <a href=\"http://www.afterpay.nl/page/consument-faq\" target=\"_blank\">http://www.afterpay.nl/page/consument-faq</a> onder het kopje \"Gegevenscontrole\". 
    	</p>
    	<p>
    		Wij adviseren u om uw aanvraag gegevens te corrigeren en het opnieuw te proberen.
    	</p>
    ";
    
    public function setRejectTemplate($id = 1) {
        $templateId = 'template' . $id;
        
        if (isset($this->$templateId)) {
            $this->_rejectTemplate = $this->$templateId;
        } else {
            $this->_rejectTemplate = $this->template1;
        }
        
        
        return $this;
    }
    
    protected function _toHtml()
    {
        return $this->_rejectTemplate;
    }
}