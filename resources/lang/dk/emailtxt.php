<?php

return [
    "user_register_email_subject" => "Du er blevet registreret | :name",
    "user_register_email_txt" => "Hej <b> :name,</b><br><br>Du er blevet tilføjet systemet. Gør venligst brug af disse login oplysninger, for at logge ind i systemet.<br>(Du kan ændre dit password, når du er logget ind, hvis du ønsker dette.)<br><br>E-mail: <b>:email</b><br>Password: <b> :password <br>Login Her: <a href=':url'>Klik her for at logge ind</a><br><br> Hvis knappen herover ikke virker, venligst gå til denne webadresse<br> :url",
    "user_password_update_email_subject" => "Din adgangskode er blevet opdateret | :name",
    "user_password_update_email_txt" => "Hej <b> :name,</b><br><br>Din adgangskode er blevet opdateret i systemet. Gør venligst brug af disse login oplysninger, for at logge ind i systemet.<br>(Du kan ændre dit password, når du er logget ind, hvis du ønsker dette.)<br><br>E-mail: <b>:email</b><br>Password: <b> :password <br>Login Her: <a href=':url'>Klik her for at logge ind</a><br><br> Hvis knappen herover ikke virker, venligst gå til denne webadresse<br> :url",
    "reminder_treatment_email_subject" => "Påmindelses E-mail | :name",
    "reminder_treatment_email_txt" => 'Hej, <b> :name</b>,<br><br>Tak fordi du gør brug af vores tilbud. Denne e-mail er for at påminde dig om denne.<br><br>Behandling:<b> :treatment</b><br>Behandlings Dato: <b> :date</b><br>Behandlings Tidspunkt: <b> :time</b><br>Din Booking Status: <b>Booked</b><br><br>Lokation: <b>:description</b><br><a href=":url">Link til webside</a><br>',
    "reminder_event_email_subject" => "Påmindelses E-mail | :name",
    "reminder_event_email_txt" => 'Hej, <b> :name </b>,<br><br>Tak fordi du gør brug af os. Denne E-mail er for at minde dig om din booking.<br>Begivenhed:<b> :event </b><br><br>Begivenheds dato<b> :date </b><br>Begivenheds tidspunkt: <b> :time </b><br>Din Booking Status: <b> :status </b><br><br><a href=":url">Link til webside</a><br>',
    "code_you_need_for_booking" => "Din booking kode er :code <br><br>",
    "free_spots_email_subject" => "Ønsker du en konsultation?",
    "free_spots_email_txt" => "Kære :name ,<br><br> Der er ledige konsulationstider i In-House klinikken. <br><br>  :content <br><br><a href=':pageLink'>Klik her for at besøge bookingsiden og se ledige tider</a><br><br> :codeMessage Har du Spørgsmål? Kontakt venligst <a href='mailto::email'> :businessName </a><br><br>Hvis du ikke ønsker at modtage mails om ledige konsultationstider, kan du afmelde dig denne service <a href=':unsubLink'>HER</a><br><br>",
    "free_event_email_subject" => "Arrangementer tilgængelige",
    "free_spots_email_txt_for_events" => "Kære :name ,<br><br> Der er ledige arrangementer <br><br>  :content <br><br><a href=':pageLink'>Klik her for at besøge bookingsiden og se tilgængelige arrangementer. </a><br><br> Har du nogle spørgsmål? Venligst kontakt <a href='mailto::email'> :businessName </a><br><br>Hvis du ikke ønsker at modtage e-mails om tilgængelig begivenhed, kan du afmelde denne tjeneste <a href=':unsubLink'>HER</a><br><br>",
    "survey_email_subject" => 'Hurtig undersøgelse | :name',
    "survey_email_txt" => 'Kære <b> :name,</b><br><br>Tak for dit besøg i vores In House Klinik.<br>
    Vi ønsker hele tiden at blive bedre og håber derfor, at du vil bruge et minut på en tilfredshedsundersøgelse.<br><br>Klik her for at udfylde den: <b><a href=":link">Gennemse undersøgelsen</a></b><br>',
    'treatment_bookin_restore_subject' => 'Booking Behandling Gendannet | :name ',
    'treatment_bookin_restore_txt' => 'Hej <b> :name ,</b><br><br>Tak fordi du gør brug af os. Denne E-mail er for at informere dig om din booking er blevet gendannet. Dine booking detaljer kan læses herunder.<b><br><br>Behandling: <b> :treatment </b><br>Behandlings dato: <b> :date </b><br>Behandlings tidspunkt: <b> :time </b><br>Din Booking Status: <b>Booked</b><br><br><a href=":url">Link til webside</a><br>',
    'treatment_booking_subject' => 'Behandling booket | :name ',
    'treatment_booking_txt' => 'Hej <b> :name ,</b><br><br>Tak fordi du gør brug af os. Se din booking herunder.<br>Behandling: <b> :treatment </b><br>Behandlings dato: <b> :date </b><br>Behandlings tidspunkt: <b> :time </b><br>Din Booking Status: <b>Booket</b><br>Lokation: <b>:description</b><br><br><b>Nuværende bookinger i systemet:</b><br><br> :bookingtable <br>',
    'treatment_booking_cancel_subject' => 'Behandling Aflyst | :name ',
    'treatment_booking_cancel_txt' => 'Hej <b> :name,</b><br><br>Tak fordi du gør brug af os. Denne E-mail er for at informere dig om din booking er blevet aflyst. Detaljer for aflysningen kan findes herunder<br><br>Behandling: <b> :treatment </b><br>Behandlings dato: <b> :date </b><br>Behandlings tidspunkt: <b> :time </b><br>Din Booking Status: <b>Aflyst</b><br><br><a href=":homeLink">Klik her for at foretage ny booking</a><br>',
    'card_created_subject' => 'Kort er blevet oprettet til | :name',
    'card_created_txt' => 'Hej <b> :name,</b><br><br>Tak fordi du gør brug af os. Denne E-mail er for at informere dig om dit nye kort er blevet oprettet. Se venligst detaljer herunder.<br><br>Kort Titel: <b> :title</b><br>Udløbs Dato: <b> :expiry </b><br>Klip på Kort: <b> :clips </b><br>Kort til brug af: <b> :for </b><br>',
    'card_clip_purchased_subject' => 'Klip er blevet tilkøbt dit klippekort | :name',
    'card_clip_purchased_txt' => 'Hej <b> :name,</b><br><br>Tak fordi du gør brug af os. Denne E-mail er for at informere dig om dit kort er blevet fratrukket <b> :purchase </b> klip. Se venligst detaljer herunder.<br><br>Kort Titel: <b> :title </b><br>Udløbs Dato: <b> :date </b><br>Klip på Kort: <b> :clips </b><br>Kort til brug af: <b> :for </b><br>',
    'clips_restore_subject' => 'Klip er blevet tilbageført på dit kort | :name',
    'clips_restore_txt' => 'Hej <b> :name ,</b><br><br>Tak fordi du gør brug af os. Denne E-mail er for at informere dig om at klip på dine kort <b> :amount </b> er blevet gendannet. Se venligst detaljer herunder.<br><br>Kort Titel: <b> :title </b><br>Udløbs Dato: <b> :expiry </b><br>Klip på Kort: <b> :clips </b><br>Kort til brug af: <b>:for</b><br>',
    'clips_used_subject' => 'Klip er blevet brug fra dit kort | :name',
    'clips_used_txt' => 'Hej <b> :name ,</b><br><br>Tak fordi du gør brug af os. Denne E-mail er for at informere dig om dit kort er blevet fratrukket <b> :used </b> klip. Se venligst detaljer herunder.<br><br>Kort Titel: <b> :title </b><br>Udløbs Dato: <b> :expiry </b><br>Klip på Kort: <b> :clips </b><br>Kort til brug af: <b> :for </b><br>',
    'event_book_subject' => 'Begivenhed booket | :name',
    'event_book_txt' => 'Hej <b> :name ,</b><br><br>Tak fordi du gør brug af os. Se venligst detaljer for din booking herunder.<br>Begivenhedens Navn: <b> :ename </b> <br>Begivenhedens Dato: <b> :date </b><br>Begivenheds Tidspunkt: <b> :time </b><br>Din Booking Status: <b> :status </b><br> :guest <br> Instruktør :instructor <br><a target="_blank" href=":link">Se reservationer</a>',
    'event_booking_status_subject' => 'Begivenheds status er ændret | :name ',
    'event_booking_status_txt' => 'Hej <b> :name ,</b><br><br>Tak fordi du gør brug af os. Denne E-mail er for at informere dig om din booking status for begivenhed er blevet ændret. Se venligst detaljer herunder.<br><br>Begivenhed: <b> :ename </b><br>Begivenhedens dato: <b> :date </b><br>Begivenheds tidspunkt: <b> :time </b><br> :status <br><br>',
    'event_booking_cancel_subject' => 'Booking til begivenhed er blevet slettet | :name ',
    'event_booking_cancel_txt' => 'Hej <b> :name ,</b><br><br>Tak fordi du gør brug af os. Denne E-mail er for at informere dig at din booking til begivenheden er blevet slettet. Se venligst detaljer herunder.<br><br>Begivenhed: <b> :ename </b><br>Begivenheds dato: <b> :date </b><br>Begivenheds tidspunkt: <b> :time </b><br> :status <br><br><a href=":homeLink">Klik her for at foretage ny booking</a><b>',
    'event_update_subject' => 'Begivenheden er opdateret | :name',
    'event_update_txt' => 'Hej <b> :name</b>,<b><br> Tak fordi du bruger vores services. Denne e-mail er for at informere dig om en opdatering til begivenheden.<br> Denne ændring er foretaget :changes. <br>Du kan, se samlet information om begivenheden herunder.<br><br> Begivenhed: <b> :ename </b> <br>Dato : <b> :date </b><br> Tidspunkt : <b> :time </b><br> Instruktør :instructor <br> <a target="_blank" href=":link">Se bookings</a>',
    'date_update_subject' => 'Behandling er opdateret | :name',
    'date_update_txt' => 'Hej <b> :name</b>,<b><br>  Tak fordi du bruger vores services. Denne e-mail er for at informere dig om en opdatering til behandling.<br> Denne ændring er foretaget :changes. <br>Du kan, se samlet information om begivenheden herunder.<br> <br>Behandling dato: <b> :date </b><br> Behandling tidspunkt: <b> :time </b><br> Instruktør: :instructor <br> <a target="_blank" href=":link">Se bookings</a>',
    'schedule_report_email_subject' => 'Planlæg rapport | :name',
    'schedule_report_email_txt' => 'Hej <b> :name</b>,<br> Se vedlagte tidsplanrapport.',
    'pdf_receipt_email_subject' => 'Kvittering for behandlingsbetaling | :name',
    'pdf_receipt_email_txt' => 'Hej <b> :name</b>,<br> Find vedhæftet betalingskvittering.',
    "waiting_list_treatment_email_subject" => "Venteliste-e-mail | :name",
    "waiting_list_treatment_email_txt" => "Dear :name ,<br><br> Tak fordi du bruger vores tjenester, denne e-mail er for at informere dig om gratis spot on :date. <br><br><a href=':pageLink'>Klik her for at besøge bookingsiden og se ledig tid</a><br><br> Har du nogle spørgsmål? Venligst kontakt <a href='mailto::email'> :businessName </a><br>",
    'mail_from' => 'Mail fra',
];