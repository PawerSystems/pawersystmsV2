<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute skal accepteres.',
    'active_url' => ':attribute er ikke en korrekt URL.',
    'after' => ':attribute skal være en dato efter :date.',
    'after_or_equal' => ':attribute skal være en dato eller identisk med :date.',
    'alpha' => ':attribute må kun indeholde bogstaver.',
    'alpha_dash' => ':attribute må kun indeholde bogstaver, tal og underscore.',
    'alpha_num' => ':attribute må kun indeholde tal og bogstaver.',
    'array' => ':attribute skal være en liste.',
    'before' => ':attribute skal være en dato før :date.',
    'before_or_equal' => ':attribute skal være en dato før eller identisk med :date.',
    'between' => [
        'numeric' => ':attribute skal være imellem :min og :max.',
        'file' => ':attribute skal være imellem :min og :max kilobytes.',
        'string' => ':attribute skal være imellem :min og :max karaktere.',
        'array' => ':attribute skal have imellem :min og :max genstande.',
    ],
    'boolean' => ':attribute feltet skal være sandt eller falsk.',
    'confirmed' => ':attribute godkendelsen er ikke identisk.',
    'date' => ':attribute er ikke en genkendt dato.',
    'date_equals' => ':attribute skal være en dato :date.',
    'date_format' => ':attribute passer ikke til formattet :format.',
    'different' => ':attribute og :other skal være forskellige.',
    'digits' => ':attribute skal være :digits decimaler.',
    'digits_between' => ':attribute skal være imellem :min og :max decimaler.',
    'dimensions' => ':attribute har ikke de rigtige billede dimensioner.',
    'distinct' => ':attribute har en dublet.',
    'email' => ':attribute skal være en godkendt email addresse.',
    'ends_with' => ':attribute skal slutte med en af følgende værdier: :values.',
    'exists' => 'Den valgte :attribute er ikke godkendt.',
    'file' => ':attribute skal være en fil.',
    'filled' => ':attribute feltet skal have en værdi.',
    'gt' => [
        'numeric' => ':attribute skal være større end :value.',
        'file' => ':attribute skal være større end :value kilobytes.',
        'string' => ':attribute skal være større end :value tegn.',
        'array' => ':attribute skal have flere end :value genstande.',
    ],
    'gte' => [
        'numeric' => ':attribute skal være større end eller samme som :value.',
        'file' => ':attribute skal være større end eller samme som :value kilobytes.',
        'string' => ':attribute skal være større end eller samme som :value tegn.',
        'array' => ':attribute skal have :value enheder eller mere.',
    ],
    'image' => ':attribute skal være et billede.',
    'in' => ' Den valgte :attribute er forkert.',
    'in_array' => ':attribute findes ikke i :other.',
    'integer' => ':attribute skal være et heltal.',
    'ip' => ':attribute skal være en gyldig IP addresse.',
    'ipv4' => ':attribute skal være en gyldig IPv4 addresse.',
    'ipv6' => ':attribute skal være en gyldig IPv6 addresse.',
    'json' => ':attribute skal være en gyldig JSON streng.',
    'lt' => [
        'numeric' => ':attribute skal være mindre end :value.',
        'file' => ':attribute skal være mindre end :value kilobytes.',
        'string' => ':attribute skal være mindre end :value tegn.',
        'array' => ':attribute skal have færre end :value enheder.',
    ],
    'lte' => [
        'numeric' => ':attribute skal være mindre end eller lig med :value.',
        'file' => ':attribute skal være mindre end eller lig med:value kilobytes.',
        'string' => ':attribute skal være mindr end eller lig med :value tegn.',
        'array' => ':attribute må ikke have flere end :value enheder.',
    ],
    'max' => [
        'numeric' => ':attribute må ikke være større end :max.',
        'file' => ':attribute må ikke være større end :max kilobytes.',
        'string' => ':attribute må ikke være større end :max tegn.',
        'array' => ':attribute må ikke have flere end :max enheder.',
    ],
    'mimes' => ':attribute skal være en af følgende filtyper: :values.',
    'mimetypes' => ':attribute skal være følgende filtype: :values.',
    'min' => [
        'numeric' => ':attribute skal være mindst :min.',
        'file' => ':attribute skal være mindst :min kilobytes.',
        'string' => ':attribute skal være mindst :min tegn.',
        'array' => ':attribute skal have mindst :min enheder.',
    ],
    'not_in' => 'Den valgte :attribute er ikke gyldig.',
    'not_regex' => ':attribute format er ikke gyldigt.',
    'numeric' => ':attribute skal være en nummer.',
    'password' => 'Password er forkert.',
    'present' => ':attribute feltet skal være til stede.',
    'regex' => ':attribute formatet er ikke gyldigt.',
    'required' => ':attribute feltet er krævet.',
    'required_if' => ':attribute feltet skal udfyldes når :other er :value.',
    'required_unless' => ':attribute feltet er krævet hvis ikke :other er i :values.',
    'required_with' => ':attribute feltet er krævet når :values er valgt.',
    'required_with_all' => ':attribute feltet er krævet når :values er valgt.',
    'required_without' => ':attribute feltet er krævet når :values ikke er valgt.',
    'required_without_all' => ':attribute feltet er krævet når ingen af :values er valgt.',
    'same' => ':attribute og :other skal være ens.',
    'size' => [
        'numeric' => ':attribute skal være :size.',
        'file' => ':attribute skal være :size kilobytes.',
        'string' => ':attribute skal være :size tegn.',
        'array' => ':attribute skal indeholde :size enheder.',
    ],
    'starts_with' => ':attribute skal starte med en af følgende: :values.',
    'string' => ':attribute skal være en streng.',
    'timezone' => ':attribute skal være en godkendt zone.',
    'unique' => ':attribute er allerede blevet taget.',
    'uploaded' => ':attribute fejlede ved upload.',
    'url' => ':attribute format er forkert.',
    'uuid' => ':attribute skal være en godkendt UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'cpass' => [
            'required' => 'Nuværende password kan ikke være tomt. ',
            'confirmed' => 'Password passer ikke med dit nuværende password',
        ],
        'npass' => [
            'required' => 'Nyt Password kan ikke være tomt. ',
            'same' => 'Nyt Password and bekræftede Password skal være det samme',
        ],
        'rpass' => [
            'required' => 'Godkend at password ikke kan være tomt. ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
