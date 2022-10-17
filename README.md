# Saityno-taikomųjų-programų-projektavimas

# Klasių nuoma užklasinei veiklai ir mokyklos tvarkaraščių sistema (Idėja)
Kiekvieno aukšto klasių ir pamokų sąrašai (tvarkaraščiai)

Kiekvienos klasės užimtumo (pamokų) grafikas

Laisvų kabinetų rezervacija užklasinei veiklai 

*Galima pridėti ir vertinimo (pažymių) sistemą*

# Sistemos paskirtis

Projekto tiklas - sujungti moksleivių pamokas ir užklasinės veiklas į vieną tvarkaraštį, bei palengvinti patalpų paieškos problemą užklasinės veiklos vedėjams/mokytojams/repetitoriams

Moksleivis norėdamas prisijungti prie svetainės turės susikūrti paskyrą ir palaukti kol ją patvirtins administratorius. Po paskyros patvirtinimo moksleivis galės matyti visas jo klasei (tuo pačiu ir jam) priskirtas pamokas ( savo tvarkaraštį ) ir pamokų aprašymą ( vedantį mokytoją, savaitines valandas ir t.t.). Taip pat moksleivis galės prisidėti užklasinės veiklas į savo tvarkaraštį.  *Galima pridėti ir vertinimo (pažymių) sistemą*

Mokytojas po paskyros patvirtinimo galės matyti jam priskirtas vesti pamokas ir juose turinčius dalyvauti moksleivius. Taip pat mokytojas gali dirbti repetitoriumi užregistruojant papildomas pamokas kaip užklasinę veiklą laisvuose kabinetuose.

# Objektai: mokykla -> aukštas -> klasė

# Rolės ir jų funkcijos: 

Sistemos administratorius:

• Naujos mokyklos pridėjimas/redagavimas/peržiūra/šalinimas

• Mokyklos administratoriaus paskyros sukūrimas/šalinimas/redagavimas/peržiūra



Mokyklos Administratorius: 

• Naujos paskyros patvirtinimas

• Paskyrų šalinimas/redagavimas/peržiura/kūrimas

• Vedančio pamoką mokytojo keitimas

• Pamokų priskirimas mokytojams

• Užklasinės veiklos kūrimo patvirtinimas

Moksleivis: 

• Tvarkaraščio peržiūra

• Kiekvieno dalyko(modulio) informacijos peržiūra

• Registracija/Atsisakymas į užklasines veiklas

Mokytojas/Užklasinės veiklos vedėjas:

• Laisvų klasių tvarkaraščio peržiūra

• Laisvų klasių rezervacija

• Užklasinės veiklos sukūrimas, aprašymas, redagavimas, šalinimas

# Architektūra

Sistemos sudedamosios dalys:
• Kliento pusė (ang. Front-End) – naudojant React.js; • Serverio pusė (angl. Back-End) – naudojant PHP Laravel. Duomenų bazė – MySQL.

![image](https://user-images.githubusercontent.com/35394736/196100637-c2e4b4f6-4a6a-40b3-a983-d212d8d91dfe.png)

