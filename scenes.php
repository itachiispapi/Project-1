<?php
/* -----------------------------------------------------------------
   Text-based RPG –  with health & inventory hooks
   -----------------------------------------------------------------
   HOW IT WORKS
   1.  'text'      – what the player reads on screen
   2.  'choices'   – button label  =>  next-scene-id
   3.  'requires_item' / 'success_scene'
        – if player LACKS that item, they stay in the current scene.
-------------------------------------------------------------------*/

$scenes = [

/* ---------- 1 ---------- */
'start' => [
    'text' => 'You wake up in a crumbling courtyard in an abandoned castle. To your LEFT, the sweet song of birds echo
    from an ivy-ridden archway. To your RIGHT, you feel the warm flames from a torch-lit tunnel. Which way will you go?',
    'choices' => [
        'left'  => 'ivy_arch',
        'right' => 'torch_tunnel'
    ],
],

/* ---------- 2 ---------- */
'ivy_arch' => [
    'text' => 'You make your way through the vines and see a mossy library, where streams of light shine
    from the broken ceiling. A flock of musical birds swirl around a glowing BOOK floating midair. Do you...',
    'choices' => [
        'read book' => 'spellbook',
        'ignore'    => 'shadow_corridor'
    ],
],

/* ---------- 3 ---------- */
'torch_tunnel' => [
    'text' => 'As you travel through the tunnel, the torch-light fades. Darkness
               surrounds you, and you slice your arm on a protruding rock (-10 HP).
               At the end of the tunnel you see a ladder and a creepy door. Which will you choose?',
    'choices' => [
        'ladder' => 'watch_tower',
        'door'   => 'sealed_door'
    ],
],

/* ---------- 4 ---------- */
'spellbook' => [
    'text' => 'When you pick up the book, you feel Arcane energy surge through you (+20 HP).
               Your only way out is down a STAIR, or a tricky climb through a WINDOW to get outside.',
    'choices' => [
        'stair'  => 'catacombs',
        'window' => 'courtyard_back'
    ],
],

/* ---------- 5 ---------- */
'shadow_corridor' => [
    'text' => 'A hole opens underneath you and swallows your soul (-15 HP). There is 
                hope, however, when you see a silver KEY glinting on the floor.',
    'choices' => [
        'take key' => 'key_taken',
        'run'      => 'catacombs'
    ],
],

/* ---------- 6 ---------- */
'key_taken' => [
    'text' => 'As you pick up the key, a magical mist fills this pit of despair, and a 
               spiral staircase is revealed...',
    'choices' => [
        'descend' => 'catacombs'
    ]
],

/* ---------- 7 ---------- */
'watch_tower' => [
    'text' => 'Arms aching from the climb, you pause to enjoy the view of the overgrown Kingdom
               from atop the Watch Tower. You notice an elven-made sword peeking from it\'s scabbard,
               lying unused on the floor.',
    'choices' => [
        'grab sword' => 'sword_taken',
        'climb down' => 'courtyard_back'
    ],
    
],

/* ---------- 8 ---------- */
'sword_taken' => [
    'text' => 'You have obtained The Blade of Slime! A rope will take you down to the courtyard.',
    'choices' => [
        'down' => 'courtyard_back'
    ]
],

/* ---------- 9 ---------- */
'sealed_door' => [
    'text' => 'A closer look at the creepy door reveales a keyhole shaped like a gaping jaw. Scary!
               The keyhole speaks! "Only one with the Silver Key may enter here!"',
    'choices' => [
        'unlock' => 'throne_room',
        'back'   => 'torch_tunnel'
    ],
    'requires_item' => 'silver_key',   // player must have this
    'success_scene' => 'throne_room'   // where to go if they do
],

/* ---------- 10 ---------- */
'courtyard_back' => [
    'text' => 'The courtyard is filled with beautiful, pungent flowers.
                Strange, fuzzy animals play by a soothing campfire, and a massive marble door
                gleams from the end of the courtyard. Do you REST by the campfire, or ENTER
                the ominous door?',
    'choices' => [
        'enter' => 'throne_room',
        'rest'  => 'campfire'
    ]
],

/* ---------- 11 ---------- */
'catacombs' => [
    'text' => 'The stairs lead you down to gloomy catacombs, full of venemous, flying rats (-20 HP).
                Ouch! Do you retreat, or try to make your way to the gate?',
    'choices' => [
        'gate'     => 'courtyard_back',
        'retreat'  => 'torch_tunnel'
    ],
],

/* ---------- 12 ---------- */
'campfire' => [
    'text' => 'The fuzzy animals cuddle with you as you rest and recover (+15 HP).',
    'choices' => [
        'proceed' => 'throne_room'
    ],
],

/* ---------- FINAL DECISION ---------- */
'throne_room' => [
    'text' => 'After the journey you\'ve had, you feel like you can face anything.
                Until you see the Undead Lord of Butts. His skeletal shadow makes you cower in fear.
               Do you FIGHT, OFFER the Key, CAST the Spell, or FLEE?',
    'choices' => [
        'fight'       => 'ending_hero',
        'offer key'   => 'ending_freedom',
        'cast spell'  => 'ending_cursed',
        'flee'        => 'ending_trapped'
    ]
],

/* ---------- ENDINGS (4) ---------- */
'ending_hero' => [
    'text' => 'Sword flashing, you defeat the Lord of Butts and free the realm from the curse. You are hailed a HERO!',
    'choices' => []
],

'ending_fail_fight' => [
    'text' => 'You charge at the lord with you bare hands. That goes about as well as you\'d think. SPLAT.',
    'choices' => []
],

'ending_freedom' => [
    'text' => 'The Lord of Butts accepts the silver key and grants your freedom — and a FAT chest of GOLD.',
    'choices' => []
],

'ending_fail_key' => [
    'text' => 'You fumble for a key you don\'t have. The Lord gives out a bellowing laugh, and banishes you to the Stank Mines. YIKES.',
    'choices' => []
],

'ending_cursed' => [
    'text' => 'Your spell backfires horrendously. Darkness consumes you. Then the flying rats eat the leftovers. You are CURSED forever.',
    'choices' => []
],

'ending_fail_spell' => [
    'text' => 'You try to cast a spell...with no spellbook. Smart. The lord ties you to the cieling. You are now a PIÑATA.',
],

'ending_trapped' => [
    'text' => 'You try to flee, but iron doors slam shut. You remain TRAPPED for eternity as the Lord of Butts\' emotional support ANIMAL',
    'choices' => []
],

];
