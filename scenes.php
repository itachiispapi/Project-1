<?php
    $scenes=[
    'start'=>[
    'text'=>'You stand before two ancient doors. Which one do you open?',
    'choices'=>['left'=>'dragon','right'=>'treasure']
    ],
    'dragon'=>[
    'text'=>'A dragon awakens! Fight or flee?',
    'choices'=>['fight'=>'ending_hero','flee'=>'ending_coward']
    ],
    'treasure'=>[
    'text'=>'A chamber of gold! Take coins or keep walking?',
    'choices'=>['take'=>'ending_rich','walk'=>'ending_curse']
    ],
    'ending_hero'=>['text'=>'You slay the beast and become a legend. The End.','choices'=>[]],
    'ending_coward'=>['text'=>'You escape but live in shame. The End.','choices'=>[]],
    'ending_rich'=>['text'=>'Riches are yours forever. The End.','choices'=>[]],
    'ending_curse'=>['text'=>'A curse befalls you. The End.','choices'=>[]]
    ];
?>
