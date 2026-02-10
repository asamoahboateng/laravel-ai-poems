<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Poem;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PoemSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = Genre::pluck('id', 'slug');
        $subjects = Subject::pluck('id', 'slug');

        $poems = [
            // === NURSERY RHYMES ===
            [
                'title' => 'Twinkle, Twinkle, Little Star',
                'content' => "Twinkle, twinkle, little star,\nHow I wonder what you are!\nUp above the world so high,\nLike a diamond in the sky.\n\nWhen the blazing sun is gone,\nWhen he nothing shines upon,\nThen you show your little light,\nTwinkle, twinkle, through the night.\n\nThen the traveller in the dark\nThanks you for your tiny spark;\nHe could not see which way to go,\nIf you did not twinkle so.\n\nIn the dark blue sky you keep,\nAnd often through my curtains peep,\nFor you never shut your eye\nTill the sun is in the sky.",
                'author' => 'Jane Taylor',
                'genre' => 'nursery-rhyme',
                'subject' => 'nature',
                'is_featured' => true,
            ],
            [
                'title' => 'Humpty Dumpty',
                'content' => "Humpty Dumpty sat on a wall,\nHumpty Dumpty had a great fall;\nAll the king's horses and all the king's men\nCouldn't put Humpty together again.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'adventure',
                'is_featured' => true,
            ],
            [
                'title' => 'Mary Had a Little Lamb',
                'content' => "Mary had a little lamb,\nIts fleece was white as snow;\nAnd everywhere that Mary went,\nThe lamb was sure to go.\n\nIt followed her to school one day,\nWhich was against the rule;\nIt made the children laugh and play\nTo see a lamb at school.\n\nAnd so the teacher turned it out,\nBut still it lingered near,\nAnd patiently waited about\nTill Mary did appear.\n\n\"Why does the lamb love Mary so?\"\nThe eager children cry;\n\"Why, Mary loves the lamb, you know,\"\nThe teacher did reply.",
                'author' => 'Sarah Josepha Hale',
                'genre' => 'nursery-rhyme',
                'subject' => 'animals',
                'is_featured' => true,
            ],
            [
                'title' => 'Jack and Jill',
                'content' => "Jack and Jill went up the hill\nTo fetch a pail of water;\nJack fell down and broke his crown,\nAnd Jill came tumbling after.\n\nUp Jack got, and home did trot,\nAs fast as he could caper;\nHe went to bed to mend his head\nWith vinegar and brown paper.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'adventure',
                'is_featured' => true,
            ],
            [
                'title' => 'Hey Diddle Diddle',
                'content' => "Hey diddle diddle,\nThe cat and the fiddle,\nThe cow jumped over the moon;\nThe little dog laughed\nTo see such sport,\nAnd the dish ran away with the spoon.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'animals',
                'is_featured' => true,
            ],
            [
                'title' => 'Little Bo-Peep',
                'content' => "Little Bo-Peep has lost her sheep,\nAnd doesn't know where to find them;\nLeave them alone, and they'll come home,\nBringing their tails behind them.\n\nLittle Bo-Peep fell fast asleep,\nAnd dreamt she heard them bleating;\nBut when she awoke, she found it a joke,\nFor they were still a-fleeting.\n\nThen up she took her little crook,\nDetermined for to find them;\nShe found them indeed, but it made her heart bleed,\nFor they'd left their tails behind them.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'Baa, Baa, Black Sheep',
                'content' => "Baa, baa, black sheep,\nHave you any wool?\nYes, sir, yes, sir,\nThree bags full;\nOne for the master,\nAnd one for the dame,\nAnd one for the little boy\nWho lives down the lane.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'Little Miss Muffet',
                'content' => "Little Miss Muffet\nSat on a tuffet,\nEating her curds and whey;\nAlong came a spider,\nWho sat down beside her,\nAnd frightened Miss Muffet away.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'Old Mother Hubbard',
                'content' => "Old Mother Hubbard\nWent to the cupboard,\nTo give the poor dog a bone;\nWhen she came there,\nThe cupboard was bare,\nAnd so the poor dog had none.\n\nShe went to the baker's\nTo buy him some bread;\nWhen she came back\nThe dog was dead.\n\nShe went to the undertaker's\nTo buy him a coffin;\nWhen she came back\nThe dog was laughing.",
                'author' => 'Sarah Catherine Martin',
                'genre' => 'nursery-rhyme',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'Three Blind Mice',
                'content' => "Three blind mice, three blind mice,\nSee how they run, see how they run!\nThey all ran after the farmer's wife,\nWho cut off their tails with a carving knife;\nDid you ever see such a thing in your life,\nAs three blind mice?",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'Little Jack Horner',
                'content' => "Little Jack Horner\nSat in the corner,\nEating a Christmas pie;\nHe put in his thumb,\nAnd pulled out a plum,\nAnd said, \"What a good boy am I!\"",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'family',
                'is_featured' => false,
            ],
            [
                'title' => 'Old King Cole',
                'content' => "Old King Cole was a merry old soul,\nAnd a merry old soul was he;\nHe called for his pipe,\nAnd he called for his bowl,\nAnd he called for his fiddlers three.\n\nEvery fiddler had a fine fiddle,\nAnd a very fine fiddle had he;\nOh, there's none so rare\nAs can compare\nWith King Cole and his fiddlers three.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'adventure',
                'is_featured' => false,
            ],
            [
                'title' => 'Georgie Porgie',
                'content' => "Georgie Porgie, pudding and pie,\nKissed the girls and made them cry;\nWhen the boys came out to play,\nGeorgie Porgie ran away.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'friendship',
                'is_featured' => false,
            ],
            [
                'title' => 'Simple Simon',
                'content' => "Simple Simon met a pieman,\nGoing to the fair;\nSays Simple Simon to the pieman,\nLet me taste your ware.\n\nSays the pieman to Simple Simon,\nShow me first your penny;\nSays Simple Simon to the pieman,\nIndeed, I have not any.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'adventure',
                'is_featured' => false,
            ],

            // === COUNTING RHYMES ===
            [
                'title' => 'One, Two, Buckle My Shoe',
                'content' => "One, two,\nBuckle my shoe;\nThree, four,\nKnock at the door;\nFive, six,\nPick up sticks;\nSeven, eight,\nLay them straight;\nNine, ten,\nA big fat hen;\nEleven, twelve,\nDig and delve;\nThirteen, fourteen,\nMaids a-courting;\nFifteen, sixteen,\nMaids in the kitchen;\nSeventeen, eighteen,\nMaids in waiting;\nNineteen, twenty,\nMy plate's empty.",
                'author' => null,
                'genre' => 'counting-rhyme',
                'subject' => 'counting',
                'is_featured' => false,
            ],
            [
                'title' => 'One, Two, Three, Four, Five',
                'content' => "One, two, three, four, five,\nOnce I caught a fish alive,\nSix, seven, eight, nine, ten,\nThen I let it go again.\n\nWhy did you let it go?\nBecause it bit my finger so.\nWhich finger did it bite?\nThis little finger on my right.",
                'author' => null,
                'genre' => 'counting-rhyme',
                'subject' => 'counting',
                'is_featured' => false,
            ],
            [
                'title' => 'This Old Man',
                'content' => "This old man, he played one,\nHe played knick-knack on my thumb;\nWith a knick-knack paddywhack,\nGive the dog a bone,\nThis old man came rolling home.\n\nThis old man, he played two,\nHe played knick-knack on my shoe;\nWith a knick-knack paddywhack,\nGive the dog a bone,\nThis old man came rolling home.\n\nThis old man, he played three,\nHe played knick-knack on my knee;\nWith a knick-knack paddywhack,\nGive the dog a bone,\nThis old man came rolling home.",
                'author' => null,
                'genre' => 'counting-rhyme',
                'subject' => 'counting',
                'is_featured' => false,
            ],

            // === LULLABIES ===
            [
                'title' => 'Rock-a-Bye Baby',
                'content' => "Rock-a-bye baby, on the treetop,\nWhen the wind blows, the cradle will rock,\nWhen the bough breaks, the cradle will fall,\nAnd down will come baby, cradle and all.\n\nBaby is drowsing, cosy and fair,\nMother sits near in her rocking chair,\nForward and back, the cradle she swings,\nAnd though baby sleeps, he hears what she sings.",
                'author' => null,
                'genre' => 'lullaby',
                'subject' => 'bedtime',
                'is_featured' => true,
            ],
            [
                'title' => 'Wynken, Blynken, and Nod',
                'content' => "Wynken, Blynken, and Nod one night\nSailed off in a wooden shoe,—\nSailed on a river of crystal light\nInto a sea of dew.\n\"Where are you going, and what do you wish?\"\nThe old moon asked the three.\n\"We have come to fish for the herring fish\nThat live in this beautiful sea;\nNets of silver and gold have we!\"\nSaid Wynken, Blynken, and Nod.\n\nThe old moon laughed and sang a song,\nAs they rocked in the wooden shoe;\nAnd the wind that sped them all night long\nRuffled the waves of dew.\nThe little stars were the herring fish\nThat lived in that beautiful sea—\n\"Now cast your nets wherever you wish,—\nNever afeard are we!\"\nSo cried the stars to the fishermen three,\nWynken, Blynken, and Nod.\n\nWynken and Blynken are two little eyes,\nAnd Nod is a little head,\nAnd the wooden shoe that sailed the skies\nIs a wee one's trundle-bed;\nSo shut your eyes while Mother sings\nOf wonderful sights that be,\nAnd you shall see the beautiful things\nAs you rock in the misty sea\nWhere the old shoe rocked the fishermen three:—\nWynken, Blynken, and Nod.",
                'author' => 'Eugene Field',
                'genre' => 'lullaby',
                'subject' => 'bedtime',
                'is_featured' => true,
            ],
            [
                'title' => 'The Land of Nod',
                'content' => "From breakfast on through all the day\nAt home among my friends I stay,\nBut every night I go abroad\nAfar into the Land of Nod.\n\nAll by myself I have to go,\nWith none to tell me what to do—\nAll alone beside the streams\nAnd up the mountain-sides of dreams.\n\nThe strangest things are there for me,\nBoth things to eat and things to see,\nAnd many frightening sights abroad\nTill morning in the Land of Nod.\n\nTry as I like to find the way,\nI never can get back by day,\nNor can remember plain and clear\nThe curious music that I hear.",
                'author' => 'Robert Louis Stevenson',
                'genre' => 'lullaby',
                'subject' => 'bedtime',
                'is_featured' => false,
            ],
            [
                'title' => 'Star Light, Star Bright',
                'content' => "Star light, star bright,\nFirst star I see tonight,\nI wish I may, I wish I might,\nHave the wish I wish tonight.",
                'author' => null,
                'genre' => 'lullaby',
                'subject' => 'bedtime',
                'is_featured' => false,
            ],

            // === ACTION RHYMES ===
            [
                'title' => 'The Itsy Bitsy Spider',
                'content' => "The itsy bitsy spider\nClimbed up the waterspout;\nDown came the rain\nAnd washed the spider out;\nOut came the sun\nAnd dried up all the rain;\nAnd the itsy bitsy spider\nClimbed up the spout again.",
                'author' => null,
                'genre' => 'action-rhyme',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'Head, Shoulders, Knees and Toes',
                'content' => "Head, shoulders, knees and toes,\nKnees and toes.\nHead, shoulders, knees and toes,\nKnees and toes.\nAnd eyes, and ears, and mouth, and nose.\nHead, shoulders, knees and toes,\nKnees and toes.",
                'author' => null,
                'genre' => 'action-rhyme',
                'subject' => 'family',
                'is_featured' => false,
            ],
            [
                'title' => 'Ring Around the Rosie',
                'content' => "Ring around the rosie,\nA pocket full of posies,\nAshes! Ashes!\nWe all fall down.\n\nThe cows are in the meadow,\nLying down and sleeping.\nThunder! Lightning!\nWe all stand up.",
                'author' => null,
                'genre' => 'action-rhyme',
                'subject' => 'friendship',
                'is_featured' => false,
            ],
            [
                'title' => 'Pat-a-Cake',
                'content' => "Pat-a-cake, pat-a-cake, baker's man,\nBake me a cake as fast as you can;\nPat it and prick it and mark it with B,\nPut it in the oven for baby and me.",
                'author' => null,
                'genre' => 'action-rhyme',
                'subject' => 'family',
                'is_featured' => false,
            ],

            // === LIMERICKS (Edward Lear) ===
            [
                'title' => 'There Was an Old Man with a Beard',
                'content' => "There was an Old Man with a beard,\nWho said, \"It is just as I feared!—\nTwo Owls and a Hen,\nFour Larks and a Wren,\nHave all built their nests in my beard!\"",
                'author' => 'Edward Lear',
                'genre' => 'limerick',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'There Was a Young Lady of Norway',
                'content' => "There was a Young Lady of Norway,\nWho casually sat in a doorway;\nWhen the door squeezed her flat,\nShe exclaimed, \"What of that?\"\nThis courageous Young Lady of Norway.",
                'author' => 'Edward Lear',
                'genre' => 'limerick',
                'subject' => 'adventure',
                'is_featured' => false,
            ],
            [
                'title' => 'There Was an Old Man in a Tree',
                'content' => "There was an Old Man in a tree,\nWho was horribly bored by a Bee;\nWhen they said, \"Does it buzz?\"\nHe replied, \"Yes, it does!\nIt's a regular brute of a Bee!\"",
                'author' => 'Edward Lear',
                'genre' => 'limerick',
                'subject' => 'animals',
                'is_featured' => false,
            ],

            // === CLASSIC CHILDREN'S POETRY ===
            [
                'title' => 'My Shadow',
                'content' => "I have a little shadow that goes in and out with me,\nAnd what can be the use of him is more than I can see.\nHe is very, very like me from the heels up to the head;\nAnd I see him jump before me, when I jump into my bed.\n\nThe funniest thing about him is the way he likes to grow—\nNot at all like proper children, which is always very slow;\nFor he sometimes shoots up taller like an India-rubber ball,\nAnd he sometimes gets so little that there's none of him at all.\n\nHe hasn't got a notion of how children ought to play,\nAnd can only make a fool of me in every sort of way.\nHe stays so close beside me, he's a coward you can see;\nI'd think shame to stick to nursie as that shadow sticks to me!\n\nOne morning, very early, before the sun was up,\nI rose and found the shining dew on every buttercup;\nBut my lazy little shadow, like an arrant sleepy-head,\nHad stayed at home behind me and was fast asleep in bed.",
                'author' => 'Robert Louis Stevenson',
                'genre' => 'free-verse',
                'subject' => 'nature',
                'is_featured' => true,
            ],
            [
                'title' => 'The Swing',
                'content' => "How do you like to go up in a swing,\nUp in the air so blue?\nOh, I do think it the pleasantest thing\nEver a child can do!\n\nUp in the air and over the wall,\nTill I can see so wide,\nRivers and trees and cattle and all\nOver the countryside—\n\nTill I look down on the garden green,\nDown on the roof so brown—\nUp in the air I go flying again,\nUp in the air and down!",
                'author' => 'Robert Louis Stevenson',
                'genre' => 'free-verse',
                'subject' => 'adventure',
                'is_featured' => true,
            ],
            [
                'title' => 'The Owl and the Pussy-Cat',
                'content' => "The Owl and the Pussy-cat went to sea\nIn a beautiful pea-green boat,\nThey took some honey, and plenty of money,\nWrapped up in a five-pound note.\nThe Owl looked up to the stars above,\nAnd sang to a small guitar,\n\"O lovely Pussy! O Pussy, my love,\nWhat a beautiful Pussy you are,\nYou are, you are!\nWhat a beautiful Pussy you are!\"\n\nPussy said to the Owl, \"You elegant fowl!\nHow charmingly sweet you sing!\nO let us be married! too long we have tarried:\nBut what shall we do for a ring?\"\nThey sailed away, for a year and a day,\nTo the land where the Bong-tree grows\nAnd there in a wood a Piggy-wig stood\nWith a ring at the end of his nose,\nHis nose, his nose,\nWith a ring at the end of his nose.",
                'author' => 'Edward Lear',
                'genre' => 'free-verse',
                'subject' => 'animals',
                'is_featured' => true,
            ],
            [
                'title' => 'The Arrow and the Song',
                'content' => "I shot an arrow into the air,\nIt fell to earth, I knew not where;\nFor, so swiftly it flew, the sight\nCould not follow it in its flight.\n\nI breathed a song into the air,\nIt fell to earth, I knew not where;\nFor who has sight so keen and strong,\nThat it can follow the flight of song?\n\nLong, long afterward, in an oak\nI found the arrow, still unbroke;\nAnd the song, from beginning to end,\nI found again in the heart of a friend.",
                'author' => 'Henry Wadsworth Longfellow',
                'genre' => 'free-verse',
                'subject' => 'friendship',
                'is_featured' => false,
            ],
            [
                'title' => 'The Rainbow',
                'content' => "Boats sail on the rivers,\nAnd ships sail on the seas;\nBut clouds that sail across the sky\nAre prettier far than these.\n\nThere are bridges on the rivers,\nAs pretty as you please;\nBut the bow that bridges heaven,\nAnd overtops the trees,\nAnd builds a road from earth to sky,\nIs prettier far than these.",
                'author' => 'Christina Rossetti',
                'genre' => 'free-verse',
                'subject' => 'nature',
                'is_featured' => false,
            ],
            [
                'title' => 'Who Has Seen the Wind?',
                'content' => "Who has seen the wind?\nNeither I nor you:\nBut when the leaves hang trembling,\nThe wind is passing through.\n\nWho has seen the wind?\nNeither you nor I:\nBut when the trees bow down their heads,\nThe wind is passing by.",
                'author' => 'Christina Rossetti',
                'genre' => 'free-verse',
                'subject' => 'nature',
                'is_featured' => false,
            ],
            [
                'title' => 'A Bird Came Down the Walk',
                'content' => "A bird came down the walk:\nHe did not know I saw;\nHe bit an angle-worm in halves\nAnd ate the fellow, raw.\n\nAnd then he drank a dew\nFrom a convenient grass,\nAnd then hopped sidewise to the wall\nTo let a beetle pass.\n\nHe glanced with rapid eyes\nThat hurried all abroad,—\nThey looked like frightened beads, I thought;\nHe stirred his velvet head\n\nLike one in danger; cautious,\nI offered him a crumb,\nAnd he unrolled his feathers\nAnd rowed him softer home\n\nThan oars divide the ocean,\nToo silver for a seam,\nOr butterflies, off banks of noon,\nLeap, splashless, as they swim.",
                'author' => 'Emily Dickinson',
                'genre' => 'free-verse',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'The Tyger',
                'content' => "Tyger Tyger, burning bright,\nIn the forests of the night;\nWhat immortal hand or eye,\nCould frame thy fearful symmetry?\n\nIn what distant deeps or skies,\nBurnt the fire of thine eyes?\nOn what wings dare he aspire?\nWhat the hand, dare seize the fire?\n\nAnd what shoulder, and what art,\nCould twist the sinews of thy heart?\nAnd when thy heart began to beat,\nWhat dread hand? and what dread feet?\n\nWhat the hammer? what the chain,\nIn what furnace was thy brain?\nWhat the anvil? what dread grasp,\nDare its deadly terrors clasp!\n\nWhen the stars threw down their spears\nAnd water'd heaven with their tears:\nDid he smile his work to see?\nDid he who made the Lamb make thee?\n\nTyger Tyger, burning bright,\nIn the forests of the night:\nWhat immortal hand or eye,\nDare frame thy fearful symmetry?",
                'author' => 'William Blake',
                'genre' => 'free-verse',
                'subject' => 'animals',
                'is_featured' => false,
            ],
            [
                'title' => 'Spring',
                'content' => "Sound the flute!\nNow it's mute.\nBirds delight\nDay and night;\nNightingale\nIn the dale,\nLark in sky,\nMerrily, merrily, to welcome in the year.\n\nLittle boy,\nFull of joy;\nLittle girl,\nSweet and small;\nCock does crow,\nSo do you;\nMerry voice,\nInfant noise,\nMerrily, merrily, to welcome in the year.\n\nLittle lamb,\nHere I am;\nCome and lick\nMy white neck;\nLet me pull\nYour soft wool;\nLet me kiss\nYour soft face;\nMerrily, merrily, we welcome in the year.",
                'author' => 'William Blake',
                'genre' => 'free-verse',
                'subject' => 'seasons',
                'is_featured' => false,
            ],
            [
                'title' => 'Bed in Summer',
                'content' => "In winter I get up at night\nAnd dress by yellow candle-light.\nIn summer, quite the other way,\nI have to go to bed by day.\n\nI have to go to bed and see\nThe birds still hopping on the tree,\nOr hear the grown-up people's feet\nStill going past me in the street.\n\nAnd does it not seem hard to you,\nWhen all the sky is clear and blue,\nAnd I should like so much to play,\nTo have to go to bed by day?",
                'author' => 'Robert Louis Stevenson',
                'genre' => 'free-verse',
                'subject' => 'seasons',
                'is_featured' => false,
            ],
            [
                'title' => 'The Lamb',
                'content' => "Little Lamb, who made thee?\nDost thou know who made thee?\nGave thee life, and bid thee feed\nBy the stream and o'er the mead;\nGave thee clothing of delight,\nSoftest clothing, woolly, bright;\nGave thee such a tender voice,\nMaking all the vales rejoice?\nLittle Lamb, who made thee?\nDost thou know who made thee?\n\nLittle Lamb, I'll tell thee,\nLittle Lamb, I'll tell thee:\nHe is called by thy name,\nFor He calls Himself a Lamb.\nHe is meek, and He is mild;\nHe became a little child.\nI a child, and thou a lamb,\nWe are called by His name.\nLittle Lamb, God bless thee!\nLittle Lamb, God bless thee!",
                'author' => 'William Blake',
                'genre' => 'free-verse',
                'subject' => 'animals',
                'is_featured' => false,
            ],

            // === HAIKU ===
            [
                'title' => 'The Old Pond',
                'content' => "An old silent pond...\nA frog jumps into the pond—\nSplash! Silence again.",
                'author' => 'Matsuo Basho',
                'genre' => 'haiku',
                'subject' => 'nature',
                'is_featured' => false,
            ],
            [
                'title' => 'Light of the Moon',
                'content' => "In the moonlight,\nThe colour and scent of the wisteria\nSeems far away.",
                'author' => 'Yosa Buson',
                'genre' => 'haiku',
                'subject' => 'nature',
                'is_featured' => false,
            ],
            [
                'title' => 'A World of Dew',
                'content' => "A world of dew,\nAnd within every dewdrop\nA world of struggle.",
                'author' => 'Kobayashi Issa',
                'genre' => 'haiku',
                'subject' => 'nature',
                'is_featured' => false,
            ],
            [
                'title' => 'Over the Wintry',
                'content' => "Over the wintry\nForest, winds howl in rage\nWith no leaves to blow.",
                'author' => 'Natsume Soseki',
                'genre' => 'haiku',
                'subject' => 'seasons',
                'is_featured' => false,
            ],

            // === MORE NURSERY RHYMES ===
            [
                'title' => 'Row, Row, Row Your Boat',
                'content' => "Row, row, row your boat,\nGently down the stream.\nMerrily, merrily, merrily, merrily,\nLife is but a dream.\n\nRow, row, row your boat,\nGently up the creek.\nIf you see a little mouse,\nDon't forget to squeak!\n\nRow, row, row your boat,\nGently down the stream.\nIf you see a crocodile,\nDon't forget to scream!",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'adventure',
                'is_featured' => false,
            ],
            [
                'title' => 'London Bridge Is Falling Down',
                'content' => "London Bridge is falling down,\nFalling down, falling down.\nLondon Bridge is falling down,\nMy fair lady.\n\nBuild it up with wood and clay,\nWood and clay, wood and clay,\nBuild it up with wood and clay,\nMy fair lady.\n\nWood and clay will wash away,\nWash away, wash away,\nWood and clay will wash away,\nMy fair lady.\n\nBuild it up with bricks and mortar,\nBricks and mortar, bricks and mortar,\nBuild it up with bricks and mortar,\nMy fair lady.",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'adventure',
                'is_featured' => false,
            ],
            [
                'title' => 'Oranges and Lemons',
                'content' => "Oranges and lemons,\nSay the bells of St. Clement's.\n\nYou owe me five farthings,\nSay the bells of St. Martin's.\n\nWhen will you pay me?\nSay the bells of Old Bailey.\n\nWhen I grow rich,\nSay the bells of Shoreditch.\n\nWhen will that be?\nSay the bells of Stepney.\n\nI do not know,\nSays the great bell of Bow.\n\nHere comes a candle to light you to bed,\nAnd here comes a chopper to chop off your head!",
                'author' => null,
                'genre' => 'nursery-rhyme',
                'subject' => 'adventure',
                'is_featured' => false,
            ],
        ];

        foreach ($poems as $index => $poem) {
            Poem::create([
                'title' => $poem['title'],
                'slug' => Str::slug($poem['title']),
                'content' => $poem['content'],
                'author' => $poem['author'],
                'genre_id' => $genres[$poem['genre']],
                'subject_id' => $subjects[$poem['subject']],
                'is_featured' => $poem['is_featured'],
                'published_at' => now()->subDays($index + 1),
            ]);
        }
    }
}
