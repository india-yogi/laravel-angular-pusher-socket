<?php
/**
 * Loco php export: Symfony (PHP array)
 * Project: Workplace Self Service
 * Release: Working copy
 * Locale: nl-BE, Nederlands
 * Tagged: Back-end
 * Exported by: Ulrik Van Schepdael
 * Exported at: Thu, 04 Jun 2020 15:23:01 +0530 
 */
return array (
  'success' => 'gelukt',
  'fail' => 'Mislukt',
  'unauthorized' => 'Niet toegelaten',
  'mdm_settings_not_found' => 'Geen default MDM instellingen gevonden!',
  'mdm_data_not_found' => 'Geen MDM verbinding gevonden!',
  'mdm_no_data_to_process' => 'Geen gegevens gevonden bij de verwerking van de AirWatch',
  'failToFetchLdapSameAccountName' => 'same account naam fetch mislukt',
  'accessDenied' => 'Toegang geweigerd, geen toestemming.',
  'authenticationFail' => 'Authentication mislukt',
  'clientNotFound' => 'client niet gevonden',
  'ldapConnectionFail' => 'Kan geen verbinding maken met de LDAP server ',
  'onlyLdapUserHaveManager' => 'Enkel LDAP gebruikers hebben een manager naam',
  'unable_to_perform_action' => 'Het verzoek kan niet uitgevoerd worden!',
  'lock_completion_message' => 'Het :action commando is naar je toestel verstuurd. Afhankelijk van de netwerkconnectie kan het even duren voor het werkt.',
  'locate_completion_message' => 'Sorry, deze actie is voorlopig niet mogelijk. Gelieve de beschikbare privé tools te raadplegen om deze actie uit te voeren.',
  'retire_completion_message' => 'Het :action commando is naar je toestel verstuurd. Zou u deze actie willen annuleren, gelieve uw toestel opnieuw te registreren.',
  'register_completion_message' => 'Het :action commando is naar het MDM platform verstuurd. Verdere instructies om uw toestel te verbinden vindt u in uw mailbox.',
  'register_error_message' => 'Registratie van uw toestel is mislukt, gelieve alle gegevens na te kijken en opnieuw te proberen. Contacteer uw helpdesk mocht het probleem zich blijven voordoen.',
  'migrate_completion_message' => 'Het :action commando is naar het MDM platform verstuurd. Verdere instructies om uw toestel te verbinden vindt u in uw mailbox.',
  'repair_already_sent_message' => 'Repair request reeds ingediend voor deze asset.',
  'repair_completion_message' => 'Uw reparatiecentrum bezorgt u de nodige informatie om uw toestel te herstellen!',
  'no_mdm_api_defined_error' => 'Geen MDM API mapping (beschikbaar voor deze actie)!',
  'mdm_host_url_not_found_error' => 'MDM host URL niet gevonden voor deze asset!',
  'mdm_details_not_found_error' => 'Geen MDM details gevonden voor deze asset!',
  'emailMustUnique' => 'Email moet een unieke waarde bevatten.',
  'usernameMustUnique' => 'Gebruikersnaam moet een unieke waarde bevatten.',
  'connectionfail' => 'Verbinding mislukt',
  'alias_exist' => 'Alias in deze taal bestaat al, pas de taal aan of kies voor een andere alias!',
  'email_template_type_exist' => 'Email template voor geselecteerd type bestaat al, kies een andere taal of verander het type!',
  'no_settings_found' => 'Geen default instellingen gevonden tijdens het verwerken van Mobileiron!',
  'no_connection_data_found' => 'Geen MDM connectiegegevens gevonden tijdens het verwerken van Mobileiron!',
  'invalid_credentials' => 'Mobileiron niet geautoriseerd met deze data, gelieve je login info te controleren',
  'no_data_found' => 'Geen gegevens gevonden tijdens het verwerken van Mobileiron!',
  'processing_done' => 'Verwerking van Mobileiron gebeurd',
  'userNameNotSupply' => 'De user heeft geen gebruikersnaam doorgegeven.',
  'passwordNotSupply' => 'De gebruiker heeft geen wachtwoord doorgegeven.',
  'created' => 'Nieuw record succesvol aangemaakt.',
  'deleted' => 'Record succesvol verwijderd.',
  'updated' => 'Record succesvol bijgewerkt.',
  'not_found' => 'Record niet gevonden.',
  'auth' => 
  array (
    'login' => 
    array (
      'success' => 'Succesvol ingelogd.',
      'failed' => 'Oeps! Gebruikersnaam of wachtwoord niet correct.',
      'deactivated' => 'Uw account is gedesactiveerd.',
      'error' => 'Er was een probleem bij het inloggen.',
    ),
    'forgot_password' => 
    array (
      'success' => 'We hebben een link gestuurd om uw wachtwoord te resetten. Check je mailbox!',
      'not_found' => 'Geen account gevonden met deze gebruikersnaam.',
      'validation' => 
      array (
        'email_not_found' => 'Deze email is niet geregistreerd.',
      ),
    ),
    'logout' => 
    array (
      'success' => 'Succesvol uitgelogd.',
      'error' => 'Er was een probleem bij het uitloggen.',
    ),
    'update_password' => 
    array (
      'old_new_is_different' => 'Uw wachtwoord komt niet overeen met het wachtwoord dat u heeft doorgegeven.',
      'old_new_is_same' => 'Uw nieuw wachtwoord kan niet hetzelfde zijn dan een oud wachtwoord.',
      'success' => 'Media bijlage succesvol toegevoegd aan incident.',
    ),
  ),
  'esp_client' => 
  array (
    'tenant' => 
    array (
      'index' => 
      array (
        'success' => '',
        'error' => '',
        'not_found' => 'Geen contact persoon gevonden',
      ),
      'store' => 
      array (
        'success' => 'Contact persoon aangemaakt.',
      ),
      'show' => 
      array (
        'not_found' => 'Geen contact persoon gevonden',
      ),
      'update' => 
      array (
        'success' => 'Contactpersoon is bijgewerkt.',
        'not_found' => 'Geen contactpersoon gevonden.',
      ),
      'destroy' => 
      array (
        'success' => 'Contactpersoon verwijderd.',
        'not_found' => 'Geen contactpersoon gevonden.',
      ),
    ),
    'license' => 
    array (
      'index' => 
      array (
        'success' => '',
        'error' => '',
        'not_found' => 'Geen licentie gevonden.',
      ),
      'store' => 
      array (
        'success' => 'Licenties aangemaakt.',
      ),
      'show' => 
      array (
        'not_found' => 'Geen licentie gevonden.',
      ),
      'update' => 
      array (
        'success' => 'Licentie is bijgewerkt.',
        'not_found' => 'Geen licentie gevonden.',
      ),
      'destroy' => 
      array (
        'success' => 'Licentie is verwijderd.',
        'not_found' => 'Geen licentie gevonden.',
      ),
    ),
    'global_field' => 
    array (
      'index' => 
      array (
        'success' => '',
        'error' => '',
        'not_found' => 'Geen globaal veld gevonden.',
      ),
      'store' => 
      array (
        'success' => 'Globaal veld aangemaakt.',
      ),
      'update' => 
      array (
        'success' => 'Globaal veld bijgewerkt.',
        'not_found' => 'Geen globaal veld gevonden.',
      ),
      'destroy' => 
      array (
        'success' => 'Globaal veld verwijderd.',
        'not_found' => 'Geen globaal veld gevonden.',
      ),
    ),
  ),
  'esp_log' => 
  array (
    'log' => 
    array (
      'index' => 
      array (
        'success' => '',
        'error' => '',
        'not_found' => 'Geen logs gevonden.',
      ),
    ),
  ),
  'crud' => 
  array (
    'index' => 
    array (
      'success' => '',
      'error' => '',
    ),
  ),
  'exception' => 'Er is een technisch probleem, probeert u het later opnieuw.',
  'validation_exception' => 'De gegevens zijn niet correct.',
  'general' => 
  array (
    'server_error' => '',
  ),
);
