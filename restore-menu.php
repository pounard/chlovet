<?php

use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/vendor/autoload.php';

$reference = <<<EOT
1	node/1	recherche	fr	1	0	-100	\N	1
2	node/2	logo-clinique-saint-clement-blanc	fr	1	0	-100	\N	2
4	node/4	la-vaccination-des-chiens	fr	1	0	-100	\N	4
5	node/4	la-vaccination/la-vaccination-des-chiens	fr	1	0	0	\N	4
6	node/5	la-maladie-de-carre-vice-redhibitoire	fr	1	0	-100	\N	5
7	node/5	la-vaccination/la-maladie-de-carre-vice-redhibitoire	fr	1	0	0	\N	5
8	node/5	la-vaccination/la-vaccination-des-chiens/la-maladie-de-carre-vice-redhibitoire	fr	1	0	0	\N	5
9	node/6	bandeau-daccueil	fr	1	0	-100	\N	6
45	node/22	les-medicaments-veterinaires/la-prescription-de-medicaments	fr	1	0	0	\N	22
3	node/3	la-vaccination	fr	1	0	-100	\N	3
10	node/7	presentation-de-la-vaccination	fr	1	0	-100	\N	7
11	node/7	la-vaccination/presentation-de-la-vaccination	fr	1	0	0	\N	7
12	node/8	les-differents-types-de-vaccins	fr	1	0	-100	\N	8
14	node/5	la-vaccination-des-chiens/la-maladie-de-carre-vice-redhibitoire	fr	1	0	0	\N	5
15	node/9	les-protocoles-de-vaccination	fr	1	0	-100	\N	9
16	node/10	la-primovaccination	fr	1	0	-100	\N	10
17	node/11	les-rappels-vaccinaux	fr	1	0	-100	\N	11
18	node/4	la-vaccination/presentation-de-la-vaccination/la-vaccination-des-chiens	fr	1	0	0	\N	4
19	node/5	la-vaccination/presentation-de-la-vaccination/la-vaccination-des-chiens/la-maladie-de-carre-vice-redhibitoire	fr	1	0	0	\N	5
20	node/9	la-vaccination/presentation-de-la-vaccination/les-protocoles-de-vaccination	fr	1	0	0	\N	9
13	node/8	la-vaccination/presentation-de-la-vaccination/les-differents-types-de-vaccins	fr	1	0	0	\N	8
21	node/10	la-vaccination/presentation-de-la-vaccination/les-protocoles-de-vaccination/la-primovaccination	fr	1	0	0	\N	10
22	node/11	la-vaccination/presentation-de-la-vaccination/les-protocoles-de-vaccination/les-rappels-vaccinaux	fr	1	0	0	\N	11
23	node/12	les-effets-indesirables-de-la-vaccination	fr	1	0	-100	\N	12
24	node/12	la-vaccination/presentation-de-la-vaccination/les-effets-indesirables-de-la-vaccination	fr	1	0	0	\N	12
25	node/13	lhepatite-de-rubarth	fr	1	0	-100	\N	13
26	node/13	la-vaccination/la-vaccination-des-chiens/lhepatite-de-rubarth	fr	1	0	0	\N	13
27	node/14	la-parvorvirose-canine-vice-redhibitoire	fr	1	0	-100	\N	14
28	node/15	la-leptospirose	fr	1	0	-100	\N	15
29	node/14	la-vaccination/la-vaccination-des-chiens/la-parvorvirose-canine-vice-redhibitoire	fr	1	0	0	\N	14
30	node/15	la-vaccination/la-vaccination-des-chiens/la-leptospirose	fr	1	0	0	\N	15
31	node/16	la-vaccination-antirabique	fr	1	0	-100	\N	16
32	node/16	la-vaccination/la-vaccination-antirabique	fr	1	0	0	\N	16
33	node/17	la-vaccination-des-chats	fr	1	0	-100	\N	17
34	node/18	la-panleucopenie-feline-vice-redhibitoire	fr	1	0	-100	\N	18
35	node/19	le-coryza-contagieux-felin	fr	1	0	-100	\N	19
36	node/20	la-leucose-feline	fr	1	0	-100	\N	20
37	node/17	la-vaccination/la-vaccination-des-chats	fr	1	0	0	\N	17
38	node/18	la-vaccination/la-vaccination-des-chats/la-panleucopenie-feline-vice-redhibitoire	fr	1	0	0	\N	18
39	node/19	la-vaccination/la-vaccination-des-chats/le-coryza-contagieux-felin	fr	1	0	0	\N	19
40	node/20	la-vaccination/la-vaccination-des-chats/la-leucose-feline	fr	1	0	0	\N	20
41	node/21	les-medicaments-veterinaires	fr	1	0	-100	\N	21
47	node/25	les-anti-inflammatoires-non-steroidiens	fr	1	0	-100	\N	25
54	node/27	les-medicaments-veterinaires/les-corticoides-anti-inflammatoires-steroidiens	fr	1	0	0	\N	27
55	node/28	les-medicaments-veterinaires/les-antiparasitaires	fr	1	0	0	\N	28
42	node/22	la-prescription-de-medicaments	fr	1	0	-100	\N	22
43	node/23	les-antibiotiques	fr	1	0	-100	\N	23
44	node/24	plan-ecoantibio	fr	1	0	-100	\N	24
48	node/26	bonne-utilisation-des-antibiotiques	fr	1	0	-100	\N	26
46	node/23	les-medicaments-veterinaires/les-antibiotiques	fr	1	0	0	\N	23
49	node/27	les-corticoides-anti-inflammatoires-steroidiens	fr	1	0	-100	\N	27
50	node/28	les-antiparasitaires	fr	1	0	-100	\N	28
51	node/29	les-antiparasitaires-externes	fr	1	0	-100	\N	29
52	node/30	les-antiparasitaires-internes	fr	1	0	-100	\N	30
53	node/25	les-medicaments-veterinaires/les-anti-inflammatoires-non-steroidiens	fr	1	0	0	\N	25
56	node/29	les-medicaments-veterinaires/les-antiparasitaires-externes	fr	1	0	0	\N	29
57	node/30	les-medicaments-veterinaires/les-antiparasitaires-internes	fr	1	0	0	\N	30
58	node/29	les-medicaments-veterinaires/les-antiparasitaires/les-antiparasitaires-externes	fr	1	0	0	\N	29
59	node/30	les-medicaments-veterinaires/les-antiparasitaires/les-antiparasitaires-internes	fr	1	0	0	\N	30
61	node/32	les-activites-veterinaires-en-france	fr	1	0	-100	\N	32
62	node/33	une-profession-reglementee	fr	1	0	-100	\N	33
63	node/32	le-metier-veterinaire/les-activites-veterinaires-en-france	fr	1	0	0	\N	32
64	node/33	le-metier-veterinaire/une-profession-reglementee	fr	1	0	0	\N	33
60	node/31	le-metier-veterinaire	fr	1	0	-100	\N	31
66	node/35	losteopathie	fr	1	0	-100	\N	35
67	node/36	le-cadre-reglementaire	fr	1	0	-100	\N	36
68	node/37	les-principes-generaux-de-losteopathie	fr	1	0	-100	\N	37
69	node/38	les-quatre-grands-principes-de-losteopathie	fr	1	0	-100	\N	38
70	node/39	la-dysfonction-osteopathique	fr	1	0	-100	\N	39
71	node/40	les-techniques-osteopathiques	fr	1	0	-100	\N	40
72	node/41	la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e	fr	1	0	-100	\N	41
73	node/36	les-medecines-douces/losteopathie/le-cadre-reglementaire	fr	1	0	0	\N	36
439	node/126	fiches-pratiques/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes	fr	1	0	0	\N	126
75	node/35	les-medecines-douces/losteopathie	fr	1	0	0	\N	35
76	node/38	les-medecines-douces/losteopathie/les-principes-generaux-de-losteopathie/les-quatre-grands-principes-de-losteopathie	fr	1	0	0	\N	38
77	node/40	les-medecines-douces/losteopathie/les-principes-generaux-de-losteopathie/les-techniques-osteopathiques	fr	1	0	0	\N	40
78	node/39	les-medecines-douces/losteopathie/les-principes-generaux-de-losteopathie/la-dysfonction-osteopathique	fr	1	0	0	\N	39
79	node/41	les-medecines-douces/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e	fr	1	0	0	\N	41
80	node/42	deroulement-general-dune-seance-de-manipulation-selon-la-met	fr	1	0	-100	\N	42
81	node/43	les-differentes-etapes-de-la-manipulation-selon-la-methode-n	fr	1	0	-100	\N	43
82	node/44	les-resultats-attendus	fr	1	0	-100	\N	44
83	node/42	les-medecines-douces/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e/deroulement-general-dune-seance-de-manipulation-selon-la-met	fr	1	0	0	\N	42
84	node/43	les-medecines-douces/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e/les-differentes-etapes-de-la-manipulation-selon-la-methode-n	fr	1	0	0	\N	43
85	node/44	les-medecines-douces/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e/les-resultats-attendus	fr	1	0	0	\N	44
86	node/45	les-indications-contre-indications-et-effets-secondaires	fr	1	0	-100	\N	45
87	node/45	les-medecines-douces/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e/les-indications-contre-indications-et-effets-secondaires	fr	1	0	0	\N	45
88	node/46	lacupuncture	fr	1	0	-100	\N	46
89	node/47	lacupuncture-scientifiquement-reconnue	fr	1	0	-100	\N	47
90	node/48	lacupuncture-pour-qui	fr	1	0	-100	\N	48
91	node/49	lacupuncture-en-pratique-pour-mon-animal	fr	1	0	-100	\N	49
92	node/47	les-medecines-douces/lacupuncture/lacupuncture-scientifiquement-reconnue	fr	1	0	0	\N	47
93	node/48	les-medecines-douces/lacupuncture/lacupuncture-pour-qui	fr	1	0	0	\N	48
94	node/49	les-medecines-douces/lacupuncture/lacupuncture-en-pratique-pour-mon-animal	fr	1	0	0	\N	49
96	node/50	lhomeopathie	fr	1	0	-100	\N	50
97	node/51	le-principe-de-similitude	fr	1	0	-100	\N	51
98	node/52	linfinitesimalite-preparation-dun-medicament-homeopathique	fr	1	0	-100	\N	52
100	node/54	le-terrain-sain	fr	1	0	-100	\N	54
101	node/55	les-diatheses	fr	1	0	-100	\N	55
102	node/56	la-consultation-homeopathique	fr	1	0	-100	\N	56
99	node/53	lindividualite	fr	1	0	-100	\N	53
103	node/51	les-medecines-douces/lhomeopathie/le-principe-de-similitude	fr	1	0	0	\N	51
104	node/52	les-medecines-douces/lhomeopathie/linfinitesimalite-preparation-dun-medicament-homeopathique	fr	1	0	0	\N	52
105	node/53	les-medecines-douces/lhomeopathie/lindividualite	fr	1	0	0	\N	53
107	node/56	les-medecines-douces/lhomeopathie/la-consultation-homeopathique	fr	1	0	0	\N	56
109	node/58	lidentification	fr	1	0	-100	\N	58
111	node/60	lidentification-par-radiofrequence-puce-electronique	fr	1	0	-100	\N	60
110	node/59	les-modalites-didentification	fr	1	0	-100	\N	59
112	node/61	lidentification-par-tatouage-dermographique	fr	1	0	-100	\N	61
113	node/62	les-voyages-letranger-au-depart-de-la-france	fr	1	0	-100	\N	62
114	node/63	voyage-vers-les-dom-tom	fr	1	0	-100	\N	63
115	node/64	voyage-en-europe	fr	1	0	-100	\N	64
117	node/66	voyager-en-irlande-au-depart-de-la-france	fr	1	0	-100	\N	66
116	node/65	voyager-au-royaume-uni-angleterre-pays-de-galle-ecosse-et-ir	fr	1	0	-100	\N	65
118	node/67	voyager-malte-au-depart-de-la-france	fr	1	0	-100	\N	67
119	node/68	voyager-en-finlande-au-depart-de-la-france	fr	1	0	-100	\N	68
106	node/50	les-medecines-douces/lhomeopathie	fr	1	0	0	\N	50
65	node/34	les-medecines-douces	fr	1	0	-100	\N	34
95	node/46	les-medecines-douces/lacupuncture	fr	1	0	0	\N	46
120	node/69	voyager-dans-un-pays-tiers-hors-ue-et-tom-en-provenance-de-l	fr	1	0	-100	\N	69
121	node/70	vente-et-cession-de-chiens-et-chats	fr	1	0	-100	\N	70
122	node/71	nouvelle-definition-de-lelevage-de-chiens-et-chats	fr	1	0	-100	\N	71
123	node/72	les-obligations-pour-etre-eleveur	fr	1	0	-100	\N	72
124	node/73	la-cession-titre-gratuit-ou-onereux-de-chiens-ou-chats	fr	1	0	-100	\N	73
125	node/60	les-devoirs-dun-bon-maitre/lidentification/les-modalites-didentification/lidentification-par-radiofrequence-puce-electronique	fr	1	0	0	\N	60
126	node/61	les-devoirs-dun-bon-maitre/lidentification/les-modalites-didentification/lidentification-par-tatouage-dermographique	fr	1	0	0	\N	61
127	node/59	les-devoirs-dun-bon-maitre/lidentification/les-modalites-didentification	fr	1	0	0	\N	59
128	node/58	les-devoirs-dun-bon-maitre/lidentification	fr	1	0	0	\N	58
129	node/63	les-voyages-letranger-au-depart-de-la-france/voyage-vers-les-dom-tom	fr	1	0	0	\N	63
130	node/67	les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-malte-au-depart-de-la-france	fr	1	0	0	\N	67
131	node/68	les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-finlande-au-depart-de-la-france	fr	1	0	0	\N	68
132	node/64	les-voyages-letranger-au-depart-de-la-france/voyage-en-europe	fr	1	0	0	\N	64
133	node/69	les-voyages-letranger-au-depart-de-la-france/voyager-dans-un-pays-tiers-hors-ue-et-tom-en-provenance-de-l	fr	1	0	0	\N	69
134	node/65	les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-au-royaume-uni-angleterre-pays-de-galle-ecosse-et-ir	fr	1	0	0	\N	65
135	node/66	les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-irlande-au-depart-de-la-france	fr	1	0	0	\N	66
136	node/71	vente-et-cession-de-chiens-et-chats/nouvelle-definition-de-lelevage-de-chiens-et-chats	fr	1	0	0	\N	71
137	node/72	vente-et-cession-de-chiens-et-chats/les-obligations-pour-etre-eleveur	fr	1	0	0	\N	72
138	node/73	vente-et-cession-de-chiens-et-chats/la-cession-titre-gratuit-ou-onereux-de-chiens-ou-chats	fr	1	0	0	\N	73
139	node/63	les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-vers-les-dom-tom	fr	1	0	0	\N	63
140	node/67	les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-malte-au-depart-de-la-france	fr	1	0	0	\N	67
141	node/65	les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-au-royaume-uni-angleterre-pays-de-galle-ecosse-et-ir	fr	1	0	0	\N	65
142	node/66	les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-irlande-au-depart-de-la-france	fr	1	0	0	\N	66
143	node/68	les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-finlande-au-depart-de-la-france	fr	1	0	0	\N	68
144	node/64	les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe	fr	1	0	0	\N	64
145	node/69	les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyager-dans-un-pays-tiers-hors-ue-et-tom-en-provenance-de-l	fr	1	0	0	\N	69
146	node/62	les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france	fr	1	0	0	\N	62
147	node/72	les-devoirs-dun-bon-maitre/vente-et-cession-de-chiens-et-chats/les-obligations-pour-etre-eleveur	fr	1	0	0	\N	72
148	node/73	les-devoirs-dun-bon-maitre/vente-et-cession-de-chiens-et-chats/la-cession-titre-gratuit-ou-onereux-de-chiens-ou-chats	fr	1	0	0	\N	73
149	node/70	les-devoirs-dun-bon-maitre/vente-et-cession-de-chiens-et-chats	fr	1	0	0	\N	70
150	node/71	les-devoirs-dun-bon-maitre/vente-et-cession-de-chiens-et-chats/nouvelle-definition-de-lelevage-de-chiens-et-chats	fr	1	0	0	\N	71
151	node/60	les-devoirs-dun-bon-maitre/lidentification/lidentification-par-radiofrequence-puce-electronique	fr	1	0	0	\N	60
152	node/61	les-devoirs-dun-bon-maitre/lidentification/lidentification-par-tatouage-dermographique	fr	1	0	0	\N	61
108	node/57	les-devoirs-dun-bon-maitre	fr	1	0	-100	\N	57
153	node/74	bienvenue	fr	1	0	-100	\N	74
163	node/4	la-medecine-preventive/la-vaccination/la-vaccination-des-chiens	fr	1	0	0	\N	4
159	node/15	la-medecine-preventive/la-vaccination/la-vaccination-des-chiens/la-leptospirose	fr	1	0	0	\N	15
165	node/18	la-medecine-preventive/la-vaccination/la-vaccination-des-chats/la-panleucopenie-feline-vice-redhibitoire	fr	1	0	0	\N	18
156	node/12	la-medecine-preventive/la-vaccination/presentation-de-la-vaccination/les-effets-indesirables-de-la-vaccination	fr	1	0	0	\N	12
158	node/14	la-medecine-preventive/la-vaccination/la-vaccination-des-chiens/la-parvorvirose-canine-vice-redhibitoire	fr	1	0	0	\N	14
164	node/5	la-medecine-preventive/la-vaccination/la-vaccination-des-chiens/la-maladie-de-carre-vice-redhibitoire	fr	1	0	0	\N	5
161	node/3	la-medecine-preventive/la-vaccination	fr	1	0	0	\N	3
162	node/17	la-medecine-preventive/la-vaccination/la-vaccination-des-chats	fr	1	0	0	\N	17
157	node/13	la-medecine-preventive/la-vaccination/la-vaccination-des-chiens/lhepatite-de-rubarth	fr	1	0	0	\N	13
160	node/16	la-medecine-preventive/la-vaccination/la-vaccination-antirabique	fr	1	0	0	\N	16
441	node/137	fiches-pratiques/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes/la-lactation-nerveuse	fr	10	0	\N	137
171	node/9	la-medecine-preventive/la-vaccination/presentation-de-la-vaccination/les-protocoles-de-vaccination	fr	1	0	0	\N	9
442	node/121	fiches-pratiques/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne	fr	1	0	0	\N	121
173	node/25	la-medecine-et-la-chirurgie-generale/les-medicaments-veterinaires/les-anti-inflammatoires-non-steroidiens	fr	1	0	0	\N	25
174	node/27	la-medecine-et-la-chirurgie-generale/les-medicaments-veterinaires/les-corticoides-anti-inflammatoires-steroidiens	fr	1	0	0	\N	27
175	node/22	la-medecine-et-la-chirurgie-generale/les-medicaments-veterinaires/la-prescription-de-medicaments	fr	1	0	0	\N	22
177	node/29	la-medecine-et-la-chirurgie-generale/les-medicaments-veterinaires/les-antiparasitaires/les-antiparasitaires-externes	fr	1	0	0	\N	29
178	node/30	la-medecine-et-la-chirurgie-generale/les-medicaments-veterinaires/les-antiparasitaires/les-antiparasitaires-internes	fr	1	0	0	\N	30
180	node/28	la-medecine-et-la-chirurgie-generale/les-medicaments-veterinaires/les-antiparasitaires	fr	1	0	0	\N	28
181	node/77	les-vers-du-chien-et-du-chat	fr	1	0	-100	\N	77
182	node/77	la-medecine-preventive/la-vaccination/les-vers-du-chien-et-du-chat	fr	1	0	0	\N	77
184	node/78	howard-et-jacob	fr	1	0	-100	\N	78
185	node/79	jacob-et-howard	fr	1	0	-100	\N	79
186	node/80	howard-et-jacob-2	fr	1	0	-100	\N	80
187	node/81	hjprofil	fr	1	0	-100	\N	81
188	node/82	hj-jeux	fr	1	0	-100	\N	82
189	node/83	hjpage-accueil	fr	1	0	-100	\N	83
190	node/84	fiches-pratiques	fr	1	0	-100	\N	84
191	node/59	fiches-pratiques/les-devoirs-dun-bon-maitre/lidentification/les-modalites-didentification	fr	1	0	0	\N	59
192	node/60	fiches-pratiques/les-devoirs-dun-bon-maitre/lidentification/lidentification-par-radiofrequence-puce-electronique	fr	1	0	0	\N	60
193	node/61	fiches-pratiques/les-devoirs-dun-bon-maitre/lidentification/lidentification-par-tatouage-dermographique	fr	1	0	0	\N	61
197	node/57	fiches-pratiques/les-devoirs-dun-bon-maitre	fr	1	0	0	\N	57
204	node/72	fiches-pratiques/les-devoirs-dun-bon-maitre/vente-et-cession-de-chiens-et-chats/les-obligations-pour-etre-eleveur	fr	1	0	0	\N	72
205	node/73	fiches-pratiques/les-devoirs-dun-bon-maitre/vente-et-cession-de-chiens-et-chats/la-cession-titre-gratuit-ou-onereux-de-chiens-ou-chats	fr	1	0	0	\N	73
207	node/71	fiches-pratiques/les-devoirs-dun-bon-maitre/vente-et-cession-de-chiens-et-chats/nouvelle-definition-de-lelevage-de-chiens-et-chats	fr	1	0	0	\N	71
209	node/33	fiches-pratiques/le-metier-veterinaire/une-profession-reglementee	fr	1	0	0	\N	33
211	node/85	les-vers-ronds-du-chien-et-du-chien	fr	1	0	-100	\N	85
212	node/86	les-ascarides	fr	1	0	-100	\N	86
169	node/8	la-medecine-preventive/la-vaccination/presentation-de-la-vaccination/les-differents-types-de-vaccins	fr	1	0	0	\N	8
166	node/7	la-medecine-preventive/la-vaccination/presentation-de-la-vaccination	fr	1	0	0	\N	7
168	node/20	la-medecine-preventive/la-vaccination/la-vaccination-des-chats/la-leucose-feline	fr	1	0	0	\N	20
198	node/58	fiches-pratiques/les-devoirs-dun-bon-maitre/lidentification	fr	1	0	0	\N	58
206	node/70	fiches-pratiques/les-devoirs-dun-bon-maitre/vente-et-cession-de-chiens-et-chats	fr	1	0	0	\N	70
199	node/63	fiches-pratiques/les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-vers-les-dom-tom	fr	1	0	0	\N	63
202	node/69	fiches-pratiques/les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyager-dans-un-pays-tiers-hors-ue-et-tom-en-provenance-de-l	fr	1	0	0	\N	69
195	node/66	fiches-pratiques/les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-irlande-au-depart-de-la-france	fr	1	0	0	\N	66
167	node/19	la-medecine-preventive/la-vaccination/la-vaccination-des-chats/le-coryza-contagieux-felin	fr	1	0	0	\N	19
201	node/64	fiches-pratiques/les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe	fr	1	0	0	\N	64
203	node/62	fiches-pratiques/les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france	fr	1	0	0	\N	62
200	node/68	fiches-pratiques/les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-finlande-au-depart-de-la-france	fr	1	0	0	\N	68
196	node/67	fiches-pratiques/les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-malte-au-depart-de-la-france	fr	1	0	0	\N	67
194	node/65	fiches-pratiques/les-devoirs-dun-bon-maitre/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-au-royaume-uni-angleterre-pays-de-galle-ecosse-et-ir	fr	1	0	0	\N	65
183	node/77	la-medecine-preventive/les-vers-du-chien-et-du-chat	fr	1	0	0	\N	77
179	node/23	la-medecine-et-la-chirurgie-generale/les-medicaments-veterinaires/les-antibiotiques	fr	1	0	0	\N	23
208	node/32	fiches-pratiques/le-metier-veterinaire/les-activites-veterinaires-en-france	fr	1	0	0	\N	32
210	node/31	fiches-pratiques/le-metier-veterinaire	fr	1	0	0	\N	31
444	node/122	fiches-pratiques/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/presentation-de-loperation	fr	1	0	0	\N	122
445	node/124	fiches-pratiques/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/quel-age-est-il-conseille-de-faire-steriliser-une-chienne	fr	1	0	0	\N	124
446	node/123	fiches-pratiques/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/quels-sont-les-risques-de-loperation	fr	1	0	0	\N	123
221	node/90	les-moyens-de-prevention	fr	1	0	-100	\N	90
448	node/127	fiches-pratiques/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes/pyometre	fr	1	00	\N	127
463	node/207	tout-savoir-sur/reproduction	fr	1	0	0	\N	207
226	node/90	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-ronds-du-chien-et-du-chien/les-moyens-de-prevention	fr	1	0	0	\N	90
457	node/205	mon-animal-est-malade	fr	1	0	-100	\N	205
230	node/93	les-ankylostomes	fr	1	0	-100	\N	93
231	node/94	les-trichures	fr	1	0	-100	\N	94
232	node/93	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-ankylostomes	fr	1	0	0	\N	93
233	node/94	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-trichures	fr	1	0	0	\N	94
236	node/95	les-parasites-externes	fr	1	0	-100	\N	95
237	node/96	les-puces	fr	1	0	-100	\N	96
440	node/120	fiches-pratiques/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles	fr	1	0	0	\N	120
239	node/95	la-medecine-preventive/les-parasites-externes	fr	1	0	0	\N	95
240	node/97	le-cycle-de-la-puce	fr	1	0	-100	\N	97
241	node/98	comment-savoir-si-mon-animal-des-puces	fr	1	0	-100	\N	98
242	node/99	comment-traiter	fr	1	0	-100	\N	99
447	node/201	fiches-pratiques/la-sterilisation-de-mon-animal/pourquoi-faire-steriliser-mon-chat	fr	1	0	0	\N	201
443	node/204	fiches-pratiques/la-sterilisation-de-mon-animal	fr	1	0	0	\N	204
246	node/103	lalimentation-et-la-nutrition	fr	1	0	-100	\N	103
247	node/103	la-medecine-preventive/lalimentation-et-la-nutrition	fr	1	0	0	\N	103
248	node/43	les-medecines-douces/losteopathie/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e/les-differentes-etapes-de-la-manipulation-selon-la-methode-n	fr	1	0	0	\N	43
249	node/44	les-medecines-douces/losteopathie/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e/les-resultats-attendus	fr	1	0	0	\N	44
250	node/45	les-medecines-douces/losteopathie/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e/les-indications-contre-indications-et-effets-secondaires	fr	1	0	0	\N	45
251	node/42	les-medecines-douces/losteopathie/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e/deroulement-general-dune-seance-de-manipulation-selon-la-met	fr	1	0	0	\N	42
253	node/104	homeopathie	fr	1	0	-100	\N	104
254	node/105	homeopathie-tubes-granules	fr	1	0	-100	\N	105
255	node/106	reglisse-chiot	fr	1	0	-100	\N	106
256	node/107	les-vers-plats-du-chien-et-du-chat	fr	1	0	-100	\N	107
258	node/109	toilettage	fr	1	0	-100	\N	109
257	node/108	dipylidium-caninum	fr	1	0	-100	\N	108
252	node/41	les-medecines-douces/losteopathie/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e	fr	1	0	0	\N	41
235	node/94	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-ronds-du-chien-et-du-chien/les-trichures	fr	1	0	0	\N	94
458	node/206	mon-animal-va-etre-opere	fr	1	0	-100	\N	206
154	node/75	la-medecine-preventive	fr	1	0	-100	\N	75
464	node/184	tout-savoir-sur/reproduction/les-soins-apres-une-mise-bas	fr	1	0	0	\N	184
465	node/183	tout-savoir-sur/reproduction/lalimentation-de-la-mere	fr	1	0	0	\N	183
170	node/10	la-medecine-preventive/la-vaccination/presentation-de-la-vaccination/les-protocoles-de-vaccination/la-primovaccination	fr	1	0	0	\N	10
261	node/107	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-plats-du-chien-et-du-chat	fr	1	0	0	\N	107
216	node/85	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-ronds-du-chien-et-du-chien	fr	1	0	0	\N	85
234	node/93	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-ronds-du-chien-et-du-chien/les-ankylostomes	fr	1	0	0	\N	93
229	node/90	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-moyens-de-prevention	fr	1	0	0	\N	90
466	node/204	tout-savoir-sur/la-sterilisation-de-mon-animal	fr	1	0	0	\N	204
469	node/184	tout-savoir-sur/les-soins-apres-une-mise-bas	fr	1	0	0	\N	184
470	node/183	tout-savoir-sur/lalimentation-de-la-mere	fr	1	0	0	\N	183
473	node/184	tout-savoir-sur/la-sexualite-de-mon-animal/les-soins-apres-une-mise-bas	fr	1	0	0	\N	184
220	node/86	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-ronds-du-chien-et-du-chien/les-ascarides	fr	1	0	0	\N	86
406	node/194	les-moyens-de-communications-du-chat	fr	1	0	-100	\N	194
172	node/76	la-medecine-et-la-chirurgie-generale	fr	1	0	-100	\N	76
449	node/126	la-medecine-et-la-chirurgie-generale/les-chirurgies-de-convenance-la-sterilisation/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes	fr	1	0	0	\N	126
278	node/113	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-plats-du-chien-et-du-chat/les-echinocoques-un-danger-pour-lhomme	fr	1	0	0	\N	113
460	node/207	reproduction	fr	1	0	-100	\N	207
333	node/209	tout-savoir-sur	fr	1	0	0	\N	209
270	node/97	la-medecine-preventive/les-parasites-externes/les-puces/le-cycle-de-la-puce	fr	1	0	0	\N	97
271	node/112	les-vers-taenia	fr	1	0	-100	\N	112
273	node/113	les-echinocoques-un-danger-pour-lhomme	fr	1	0	-100	\N	113
155	node/11	la-medecine-preventive/la-vaccination/presentation-de-la-vaccination/les-protocoles-de-vaccination/les-rappels-vaccinaux	fr	1	0	0	\N	11
279	node/116	coton-de-tuleard-avec-sa-mere	fr	1	0	-100	\N	116
280	node/117	chirugie-bistouri	fr	1	0	-100	\N	117
281	node/118	dr-chloe-guenegan-et-howard	fr	1	0	-100	\N	118
282	node/119	dr-chloe-guenegan-et-howard-2	fr	1	0	-100	\N	119
283	node/120	la-sterilisation-des-chiens-males-et-femelles	fr	1	0	-100	\N	120
284	node/121	la-sterilisation-de-la-chienne	fr	1	0	-100	\N	121
285	node/122	presentation-de-loperation	fr	1	0	-100	\N	122
286	node/123	quels-sont-les-risques-de-loperation	fr	1	0	-100	\N	123
287	node/124	quel-age-est-il-conseille-de-faire-steriliser-une-chienne	fr	1	0	-100	\N	124
292	node/123	la-medecine-et-la-chirurgie-generale/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/quels-sont-les-risques-de-loperation	fr	1	0	0	\N	123
325	node/139	formalites-pour-voyager-dans-lue	fr	1	0	-100	\N	139
296	node/126	les-indications-medicales-de-la-sterilisation-des-chiennes	fr	1	0	-100	\N	126
297	node/127	pyometre	fr	1	0	-100	\N	127
302	node/126	la-medecine-et-la-chirurgie-generale/les-indications-medicales-de-la-sterilisation-des-chiennes	fr	1	0	0	\N	126
291	node/124	la-medecine-et-la-chirurgie-generale/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/quel-age-est-il-conseille-de-faire-steriliser-une-chienne	fr	1	0	0	\N	124
305	node/130	tetee-chatons	fr	1	0	-100	\N	130
306	node/131	hygiene-canine	fr	1	0	-100	\N	131
307	node/132	cesarienne-chatte	fr	1	0	-100	\N	132
308	node/133	tetee-gros-plan	fr	1	0	-100	\N	133
310	node/134	chiots-bouledoges-fr	fr	1	0	-100	\N	134
311	node/136	mes-rappels-vetos	fr	1	0	-100	\N	136
272	node/112	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-plats-du-chien-et-du-chat/les-vers-taenia	fr	1	0	0	\N	112
263	node/108	la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-plats-du-chien-et-du-chat/dipylidium-caninum	fr	1	0	0	\N	108
320	node/137	la-lactation-nerveuse	fr	1	0	-100	\N	137
321	node/137	la-medecine-et-la-chirurgie-generale/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes/la-lactation-nerveuse	fr	1	00	\N	137
326	node/140	la-consultation-vaccinale	fr	1	0	-100	\N	140
294	node/121	la-medecine-et-la-chirurgie-generale/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne	fr	1	0	0	\N	121
303	node/126	la-medecine-et-la-chirurgie-generale/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes	fr	1	0	0	\N	126
323	node/138	le-deroulement-general-dune-operation	fr	1	0	-100	\N	138
289	node/120	la-medecine-et-la-chirurgie-generale/la-sterilisation-des-chiens-males-et-femelles	fr	1	0	0	\N	120
304	node/127	la-medecine-et-la-chirurgie-generale/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes/pyometre	fr	1	0	0	\N	127
327	node/141	puce	fr	1	0	-100	\N	141
290	node/122	la-medecine-et-la-chirurgie-generale/la-sterilisation-des-chiens-males-et-femelles/la-sterilisation-de-la-chienne/presentation-de-loperation	fr	1	0	0	\N	122
265	node/98	la-medecine-preventive/les-parasites-externes/les-puces/comment-savoir-si-mon-animal-des-puces	fr	1	0	0	\N	98
266	node/99	la-medecine-preventive/les-parasites-externes/les-puces/comment-traiter	fr	1	0	0	\N	99
334	node/145	la-reproduction-de-ma-chienne	fr	1	0	-100	\N	145
335	node/142	tout-savoir-sur/la-reproduction-de-ma-chienne/le-cycle-sexuel-de-la-chienne	fr	1	0	0	\N	142
336	node/143	tout-savoir-sur/la-reproduction-de-ma-chienne/faire-reproduire-ma-chienne	fr	1	0	0	\N	143
331	node/142	le-cycle-sexuel-de-la-chienne	fr	1	0	-100	\N	142
337	node/145	tout-savoir-sur/la-reproduction-de-ma-chienne	fr	1	0	0	\N	145
338	node/142	fiches-pratiques/tout-savoir-sur/la-reproduction-de-ma-chienne/le-cycle-sexuel-de-la-chienne	fr	1	0	0	\N	142
339	node/144	fiches-pratiques/tout-savoir-sur	fr	1	0	0	\N	144
238	node/96	la-medecine-preventive/les-parasites-externes/les-puces	fr	1	0	0	\N	96
384	node/183	lalimentation-de-la-mere	fr	1	0	-100	\N	183
342	node/146	puce-microscope-x40	fr	1	0	-100	\N	146
343	node/147	puce-x40	fr	1	0	-100	\N	147
344	node/121	fiches-pratiques/tout-savoir-sur/la-reproduction-de-ma-chienne/la-sterilisation-de-la-chienne	fr	1	0	0	\N	121
340	node/143	fiches-pratiques/tout-savoir-sur/la-reproduction-de-ma-chienne/faire-reproduire-ma-chienne	fr	1	0	0	\N	143
397	node/185	fiches-pratiques/les-mammites	fr	1	0	0	\N	185
373	node/172	consultation	fr	1	0	-100	\N	172
399	node/186	fiches-pratiques/leclampsie	fr	1	0	0	\N	186
374	node/173	echographe	fr	1	0	-100	\N	173
375	node/174	microscope	fr	1	0	-100	\N	174
376	node/175	chirurgie	fr	1	0	-100	\N	175
347	node/149	confirmer-et-suivre-la-gestation-de-ma-chienne	fr	1	0	-100	\N	149
377	node/176	vue-exterieure	fr	1	0	-100	\N	176
378	node/177	bureau	fr	1	0	-100	\N	177
379	node/178	chirurgie2	fr	1	0	-100	\N	178
391	node/149	fiches-pratiques/la-reproduction-de-ma-chienne/confirmer-et-suivre-la-gestation-de-ma-chienne	fr	1	0	0	\N	149
349	node/150	suivre-la-gestation-de-ma-chienne	fr	1	0	-100	\N	150
351	node/151	se-preparer-laccouchement	fr	1	0	-100	\N	151
341	node/145	fiches-pratiques/tout-savoir-sur/la-reproduction-de-ma-chienne	fr	1	0	0	\N	145
353	node/152	distribution-leptospirose	fr	1	0	-100	\N	152
348	node/149	fiches-pratiques/tout-savoir-sur/la-reproduction-de-ma-chienne/confirmer-et-suivre-la-gestation-de-ma-chienne	fr	1	0	0	\N	149
352	node/151	fiches-pratiques/tout-savoir-sur/la-reproduction-de-ma-chienne/se-preparer-laccouchement	fr	1	0	0	\N	151
354	node/153	ragondin	fr	1	0	-100	\N	153
355	node/154	howard-qui-renifle	fr	1	0	-100	\N	154
356	node/155	contact-nez-nez	fr	1	0	-100	\N	155
357	node/156	jacob-bandeau-vertical	fr	1	0	-100	\N	156
358	node/157	jacob2	fr	1	0	-100	\N	157
359	node/158	gaspard2	fr	1	0	-100	\N	158
360	node/159	chiens	fr	1	0	-100	\N	159
361	node/160	vaccination-rage	fr	1	0	-100	\N	160
362	node/161	vermifuger-tot	fr	1	0	-100	\N	161
74	node/37	les-medecines-douces/losteopathie/les-principes-generaux-de-losteopathie	fr	1	0	0	\N	37
363	node/162	la-clinique	fr	1	0	-100	\N	162
364	node/163	informations	fr	1	0	-100	\N	163
365	node/164	mentions-legales	fr	1	0	-100	\N	164
366	node/165	plan-du-site	fr	1	0	-100	\N	165
461	node/208	la-sexualite-de-mon-animal	fr	1	0	-100	\N	208
368	node/167	les-locaux	fr	1	0	-100	\N	167
371	node/170	logo-clinique-saint-clement-noir	fr	1	0	-100	\N	170
380	node/179	chenil	fr	1	0	-100	\N	179
372	node/171	mathilde-fresnel	fr	1	0	-100	\N	171
392	node/143	fiches-pratiques/la-reproduction-de-ma-chienne/faire-reproduire-ma-chienne	fr	1	0	0	\N	143
386	node/184	les-soins-apres-une-mise-bas	fr	1	0	-100	\N	184
393	node/151	fiches-pratiques/la-reproduction-de-ma-chienne/se-preparer-laccouchement	fr	1	0	0	\N	151
395	node/183	la-medecine-preventive/lalimentation-et-la-nutrition/lalimentation-de-la-mere	fr	1	0	0	\N	183
381	node/180	chir	fr	1	0	-100	\N	180
382	node/181	materiel	fr	1	0	-100	\N	181
387	node/184	fiches-pratiques/tout-savoir-sur/les-soins-apres-une-mise-bas	fr	1	0	0	\N	184
398	node/186	leclampsie	fr	1	0	-100	\N	186
370	node/169	les-services	fr	1	0	-100	\N	169
369	node/168	lequipe	fr	1	0	-100	\N	168
388	node/31	fiches-pratiques/tout-savoir-sur/le-metier-veterinaire	fr	1	0	0	\N	31
467	node/185	tout-savoir-sur/les-mammites	fr	1	0	0	\N	185
389	node/142	fiches-pratiques/la-reproduction-de-ma-chienne/le-cycle-sexuel-de-la-chienne	fr	1	0	0	\N	142
468	node/186	tout-savoir-sur/leclampsie	fr	1	0	0	\N	186
350	node/150	fiches-pratiques/tout-savoir-sur/la-reproduction-de-ma-chienne/suivre-la-gestation-de-ma-chienne	fr	1	0	0	\N	150
390	node/184	fiches-pratiques/les-soins-apres-une-mise-bas	fr	1	0	0	\N	184
396	node/185	les-mammites	fr	1	0	-100	\N	185
400	node/187	lallaitement-des-nouveau-nes	fr	1	0	-100	\N	187
401	node/187	la-medecine-preventive/lalimentation-et-la-nutrition/lallaitement-des-nouveau-nes	fr	1	0	0	\N	187
471	node/210	les-maladies-liees-la-reproduction	fr	1	0	-100	\N	210
403	node/190	les-troubles-du-comportement-du-chat	fr	1	0	-100	\N	190
404	node/192	le-comportement-du-chat	fr	1	0	-100	\N	192
405	node/193	le-developpement-comportemental-du-chat	fr	1	0	-100	\N	193
383	node/182	la-vaccination-au-cabinet-saint-clement	fr	1	0	-100	\N	182
407	node/195	comportement-et-education	fr	1	0	-100	\N	195
409	node/190	comportement-et-education/le-comportement-du-chat/les-troubles-du-comportement-du-chat	fr	1	0	0	\N	190
410	node/192	comportement-et-education/le-comportement-du-chat	fr	1	0	0	\N	192
411	node/193	comportement-et-education/le-comportement-du-chat/le-developpement-comportemental-du-chat	fr	1	0	0	\N	193
408	node/194	comportement-et-education/le-comportement-du-chat/les-moyens-de-communications-du-chat	fr	1	0	0	\N	194
412	node/196	les-champs-territoriaux-du-chat	fr	1	0	-100	\N	196
413	node/196	comportement-et-education/le-comportement-du-chat/les-champs-territoriaux-du-chat	fr	1	0	0	\N	196
414	node/197	comment-bien-vivre-avec-son-chat	fr	1	0	-100	\N	197
417	node/194	comportement-et-education/comment-bien-vivre-avec-son-chat/le-comportement-du-chat/les-moyens-de-communications-du-chat	fr	1	0	0	\N	194
418	node/190	comportement-et-education/comment-bien-vivre-avec-son-chat/le-comportement-du-chat/les-troubles-du-comportement-du-chat	fr	1	0	0	\N	190
419	node/192	comportement-et-education/comment-bien-vivre-avec-son-chat/le-comportement-du-chat	fr	1	0	0	\N	192
420	node/193	comportement-et-education/comment-bien-vivre-avec-son-chat/le-comportement-du-chat/le-developpement-comportemental-du-chat	fr	1	0	0	\N	193
421	node/198	comment-amenager-le-territoire-de-votre-chat	fr	1	0	-100	\N	198
422	node/198	comportement-et-education/comment-bien-vivre-avec-son-chat/le-comportement-du-chat/comment-amenager-le-territoire-de-votre-chat	fr	1	0	0	\N	198
430	node/199	comportement-et-education/comment-bien-vivre-avec-son-chat/trucs-et-astuces-pour	fr	1	0	0	\N	199
431	node/198	comportement-et-education/comment-bien-vivre-avec-son-chat/trucs-et-astuces-pour/comment-amenager-le-territoire-de-votre-chat	fr	1	0	0	\N	198
415	node/196	comportement-et-education/comment-bien-vivre-avec-son-chat/le-comportement-du-chat/les-champs-territoriaux-du-chat	fr	1	0	0	\N	196
450	node/137	la-medecine-et-la-chirurgie-generale/les-chirurgies-de-convenance-la-sterilisation/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes/la-lactation-nerveuse	fr	1	00	\N	137
451	node/121	la-medecine-et-la-chirurgie-generale/les-chirurgies-de-convenance-la-sterilisation/la-sterilisation-de-la-chienne	fr	1	0	0	\N	121
452	node/122	la-medecine-et-la-chirurgie-generale/les-chirurgies-de-convenance-la-sterilisation/la-sterilisation-de-la-chienne/presentation-de-loperation	fr	1	0	0	\N	122
432	node/200	pour-empecher-mon-chat-detre-malpropre	fr	1	0	-100	\N	200
453	node/124	la-medecine-et-la-chirurgie-generale/les-chirurgies-de-convenance-la-sterilisation/la-sterilisation-de-la-chienne/quel-age-est-il-conseille-de-faire-steriliser-une-chienne	fr	1	0	0	\N	124
416	node/197	comportement-et-education/comment-bien-vivre-avec-son-chat	fr	1	0	0	\N	197
454	node/123	la-medecine-et-la-chirurgie-generale/les-chirurgies-de-convenance-la-sterilisation/la-sterilisation-de-la-chienne/quels-sont-les-risques-de-loperation	fr	1	0	0	\N	123
423	node/196	comportement-et-education/comment-bien-vivre-avec-son-chat/les-champs-territoriaux-du-chat	fr	1	0	0	\N	196
424	node/190	comportement-et-education/comment-bien-vivre-avec-son-chat/les-troubles-du-comportement-du-chat	fr	1	0	0	\N	190
425	node/198	comportement-et-education/comment-bien-vivre-avec-son-chat/les-moyens-de-communications-du-chat/comment-amenager-le-territoire-de-votre-chat	fr	1	0	0	\N	198
426	node/194	comportement-et-education/comment-bien-vivre-avec-son-chat/les-moyens-de-communications-du-chat	fr	1	0	0	\N	194
427	node/193	comportement-et-education/comment-bien-vivre-avec-son-chat/le-developpement-comportemental-du-chat	fr	1	0	0	\N	193
456	node/127	la-medecine-et-la-chirurgie-generale/les-chirurgies-de-convenance-la-sterilisation/la-sterilisation-de-la-chienne/les-indications-medicales-de-la-sterilisation-des-chiennes/pyometre	fr	1	0	0	\N	127
428	node/198	comportement-et-education/comment-bien-vivre-avec-son-chat/comment-amenager-le-territoire-de-votre-chat	fr	1	0	0	\N	198
429	node/199	trucs-et-astuces-pour	fr	1	0	-100	\N	199
433	node/200	comportement-et-education/comment-bien-vivre-avec-son-chat/trucs-et-astuces-pour/pour-empecher-mon-chat-detre-malpropre	fr	1	0	0	\N	200
434	node/201	pourquoi-faire-steriliser-mon-chat	fr	1	0	-100	\N	201
176	node/21	la-medecine-et-la-chirurgie-generale/les-medicaments-veterinaires	fr	1	0	0	\N	21
435	node/201	la-medecine-et-la-chirurgie-generale/pourquoi-faire-steriliser-mon-chat	fr	1	0	0	\N	201
436	node/202	les-services-proposes-au-cabinet-saint-clement	fr	1	0	-100	\N	202
324	node/138	la-medecine-et-la-chirurgie-generale/le-deroulement-general-dune-operation	fr	1	0	0	\N	138
455	node/203	la-medecine-et-la-chirurgie-generale/les-chirurgies-de-convenance-la-sterilisation	fr	1	0	0	\N	203
437	node/203	les-chirurgies-de-convenance-la-sterilisation	fr	1	0	-100	\N	203
438	node/204	la-sterilisation-de-mon-animal	fr	1	0	-100	\N	204
394	node/145	fiches-pratiques/la-reproduction-de-ma-chienne	fr	1	0	0	\N	145
472	node/145	tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne	fr	1	0	0	\N	145
385	node/184	les-soins-apres-mise-bas	fr	1	0	-100	2017-04-30 18:38:19	184
367	node/188	nous-contacter	fr	1	0	0	\N	188
474	node/183	tout-savoir-sur/la-sexualite-de-mon-animal/lalimentation-de-la-mere	fr	1	0	0	\N	183
475	node/185	tout-savoir-sur/la-sexualite-de-mon-animal/les-maladies-liees-la-reproduction/les-mammites	fr	1	0	0	\N	185
476	node/210	tout-savoir-sur/la-sexualite-de-mon-animal/les-maladies-liees-la-reproduction	fr	1	0	0	\N	210
477	node/204	tout-savoir-sur/la-sexualite-de-mon-animal/la-sterilisation-de-mon-animal	fr	1	0	0	\N	204
478	node/186	tout-savoir-sur/la-sexualite-de-mon-animal/les-maladies-liees-la-reproduction/leclampsie	fr	1	0	0	\N	186
479	node/149	tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne/confirmer-et-suivre-la-gestation-de-ma-chienne	fr	1	0	0	\N	149
481	node/142	tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne/le-cycle-sexuel-de-la-chienne	fr	1	0	0	\N	142
482	node/143	tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne/faire-reproduire-ma-chienne	fr	1	0	0	\N	143
483	node/120	tout-savoir-sur/la-sexualite-de-mon-animal/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles	fr	1	0	0	\N	120
484	node/201	tout-savoir-sur/la-sexualite-de-mon-animal/la-sterilisation-de-mon-animal/pourquoi-faire-steriliser-mon-chat	fr	1	0	0	\N	201
485	node/65	tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-au-royaume-uni-angleterre-pays-de-galle-ecosse-et-ir	fr	1	0	0	\N	65
486	node/66	tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-irlande-au-depart-de-la-france	fr	1	0	0	\N	66
487	node/67	tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-malte-au-depart-de-la-france	fr	1	0	0	\N	67
488	node/60	tout-savoir-sur/lidentification/lidentification-par-radiofrequence-puce-electronique	fr	1	0	0	\N	60
489	node/61	tout-savoir-sur/lidentification/lidentification-par-tatouage-dermographique	fr	1	0	0	\N	61
491	node/59	tout-savoir-sur/lidentification/les-modalites-didentification	fr	1	0	0	\N	59
492	node/63	tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-vers-les-dom-tom	fr	1	0	0	\N	63
493	node/68	tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-finlande-au-depart-de-la-france	fr	1	0	0	\N	68
494	node/64	tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe	fr	1	0	0	\N	64
495	node/69	tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyager-dans-un-pays-tiers-hors-ue-et-tom-en-provenance-de-l	fr	1	0	0	\N	69
497	node/70	tout-savoir-sur/vente-et-cession-de-chiens-et-chats	fr	1	0	0	\N	70
480	node/151	tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne/se-preparer-laccouchement	fr	1	0	0	\N	151
496	node/62	tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france	fr	1	0	0	\N	62
490	node/58	tout-savoir-sur/lidentification	fr	1	0	0	\N	58
498	node/212	le-cycle-sexuel-de-la-chatte	fr	1	0	-100	\N	212
462	node/208	tout-savoir-sur/la-sexualite-de-mon-animal	fr	1	0	0	\N	208
499	node/211	les-services/le-cycle-sexuel-de-la-chienne	fr	1	0	0	\N	211
EOT;

$input = <<<EOT
<ul class="menu tree-admin">
  <li><a class="tree-item active" href="/tout-savoir-sur">Tout savoir sur...</a>
    <ul class="menu tree-admin">
      <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal">La sexualit� de mon animal</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne">La reproduction de ma chienne</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne/le-cycle-sexuel-de-la-chienne">Le cycle sexuel de ma chienne</a></li>
              <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne/faire-reproduire-ma-chienne">Pr�voir l'accouplement de ma chienne</a></li>
              <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne/confirmer-et-suivre-la-gestation-de-ma-chienne">Confirmer et suivre la
                  gestation de ma chienne</a></li>
              <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/la-reproduction-de-ma-chienne/se-preparer-laccouchement">Se pr�parer � l'accouchement</a></li>
            </ul></li>
          <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/les-soins-apres-une-mise-bas">Les soins apr�s une mise bas</a></li>
          <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/lalimentation-de-la-mere">L'alimentation de la m�re</a></li>
          <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/la-sterilisation-de-mon-animal">La st�rilisation de mon animal</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/la-sterilisation-de-mon-animal/pourquoi-faire-steriliser-mon-chat">Pourquoi st�riliser mon / ma
                  chat/chatte ?</a></li>
              <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/la-sterilisation-de-mon-animal/la-sterilisation-des-chiens-males-et-femelles">Pourquoi st�riliser
                  mon / ma chien/chienne ?</a></li>
            </ul></li>
          <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/les-maladies-liees-la-reproduction">Les maladies li�es � la reproduction</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/les-maladies-liees-la-reproduction/leclampsie">L��clampsie</a></li>
              <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/les-maladies-liees-la-reproduction/les-mammites">Les mammites</a></li>
            </ul></li>
        </ul></li>
      <li><a class="tree-item" href="/tout-savoir-sur/lidentification">L'identification</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/tout-savoir-sur/lidentification/les-modalites-didentification">Les modalit�s d'identification</a></li>
          <li><a class="tree-item" href="/tout-savoir-sur/lidentification/lidentification-par-radiofrequence-puce-electronique">L'identification par radiofr�quence (� puce �lectronique)</a></li>
          <li><a class="tree-item" href="/tout-savoir-sur/lidentification/lidentification-par-tatouage-dermographique">L'identification par tatouage dermographique</a></li>
        </ul></li>
      <li><a class="tree-item" href="/tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france">Les voyages � l'�tranger au d�part de la France</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-vers-les-dom-tom">Voyage vers les DOM-TOM</a></li>
          <li><a class="tree-item" href="/tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe">Voyage en Europe</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-au-royaume-uni-angleterre-pays-de-galle-ecosse-et-ir">Voyager
                  au Royaume Uni* au d�part de la France</a></li>
              <li><a class="tree-item" href="/tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-irlande-au-depart-de-la-france">Voyager en Irlande au
                  d�part de la France</a></li>
              <li><a class="tree-item" href="/tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-malte-au-depart-de-la-france">Voyager � Malte au d�part
                  de la France</a></li>
              <li><a class="tree-item" href="/tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyage-en-europe/voyager-en-finlande-au-depart-de-la-france">Voyager en Finlande
                  au d�part de la France</a></li>
            </ul></li>
          <li><a class="tree-item" href="/tout-savoir-sur/les-voyages-letranger-au-depart-de-la-france/voyager-dans-un-pays-tiers-hors-ue-et-tom-en-provenance-de-l">Voyager dans un pays
              tiers (hors UE et TOM) en provenance de la France</a></li>
        </ul></li>
      <li><a class="tree-item" href="/tout-savoir-sur/vente-et-cession-de-chiens-et-chats">La cession � titre gratuit ou on�reux de chiens et chats</a></li>
    </ul></li>
  <li><a class="tree-item" href="/la-medecine-preventive">Pr�vention</a>
    <ul class="menu tree-admin">
      <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination">La vaccination</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/presentation-de-la-vaccination">Pr�sentation de la vaccination</a></li>
          <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chiens">La vaccination des chiens</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chiens/la-maladie-de-carre-vice-redhibitoire">La maladie de Carr�</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chiens/lhepatite-de-rubarth">L�h�patite de Rubarth</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chiens/la-parvorvirose-canine-vice-redhibitoire">La parvorvirose canine</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chiens/la-leptospirose">La leptospirose</a></li>
            </ul></li>
          <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chats">La vaccination des chats</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chats/la-panleucopenie-feline-vice-redhibitoire">La panleucop�nie f�line</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chats/le-coryza-contagieux-felin">Le coryza contagieux f�lin</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-des-chats/la-leucose-feline">La leucose f�line</a></li>
            </ul></li>
          <li><a class="tree-item" href="/la-medecine-preventive/la-vaccination/la-vaccination-antirabique">La vaccination antirabique</a></li>
        </ul></li>
      <li><a class="tree-item" href="/la-medecine-preventive/les-vers-du-chien-et-du-chat">Les vers du chien et du chat</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-ronds-du-chien-et-du-chien">Les vers ronds du chien et du chien</a></li>
          <li><a class="tree-item" href="/la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-plats-du-chien-et-du-chat">Les vers plats du chien et du chat</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-plats-du-chien-et-du-chat/dipylidium-caninum">Dipylidium caninum</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-plats-du-chien-et-du-chat/les-echinocoques-un-danger-pour-lhomme">Les �chinocoques
                  : un danger pour l�Homme</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/les-vers-du-chien-et-du-chat/les-vers-plats-du-chien-et-du-chat/les-vers-taenia">Les vers Taenia</a></li>
            </ul></li>
          <li><a class="tree-item" href="/la-medecine-preventive/les-vers-du-chien-et-du-chat/les-moyens-de-prevention">Les moyens de pr�vention</a></li>
        </ul></li>
      <li><a class="tree-item" href="/la-medecine-preventive/les-parasites-externes">Les parasites externes</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/la-medecine-preventive/les-parasites-externes/les-puces">Les puces</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/la-medecine-preventive/les-parasites-externes/les-puces/le-cycle-de-la-puce">Le cycle de la puce</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/les-parasites-externes/les-puces/comment-savoir-si-mon-animal-des-puces">Mon animal a-t-il des puces ?</a></li>
              <li><a class="tree-item" href="/la-medecine-preventive/les-parasites-externes/les-puces/comment-traiter">Comment traiter ?</a></li>
            </ul></li>
        </ul></li>
      <li><a class="tree-item" href="/la-medecine-preventive/lalimentation-et-la-nutrition">L'alimentation et la nutrition</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/lalimentation-de-la-mere">L'alimentation de la m�re</a></li>
          <li><a class="tree-item" href="/la-medecine-preventive/lalimentation-et-la-nutrition/lallaitement-des-nouveau-nes">L�allaitement des nouveau-n�s</a></li>
        </ul></li>
    </ul></li>
  <li><a class="tree-item" href="/les-medecines-douces">M�decines douces</a>
    <ul class="menu tree-admin">
      <li><a class="tree-item" href="/les-medecines-douces/losteopathie">L'ost�opathie</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/les-medecines-douces/losteopathie/le-cadre-reglementaire">Le cadre r�glementaire</a></li>
          <li><a class="tree-item" href="/les-medecines-douces/losteopathie/les-principes-generaux-de-losteopathie">Les principes g�n�raux de l'ost�opathie</a></li>
          <li><a class="tree-item" href="/les-medecines-douces/losteopathie/la-methode-niromathe-methode-de-reflexotherapie-vertebrale-e">La m�thode Niromath�</a></li>
        </ul></li>
      <li><a class="tree-item" href="/les-medecines-douces/lacupuncture">L�acupuncture</a></li>
      <li><a class="tree-item" href="/les-medecines-douces/lhomeopathie">L'hom�opathie</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/les-medecines-douces/lhomeopathie/le-principe-de-similitude">Le principe de similitude</a></li>
          <li><a class="tree-item" href="/les-medecines-douces/lhomeopathie/linfinitesimalite-preparation-dun-medicament-homeopathique">L�infinit�simalit�</a></li>
          <li><a class="tree-item" href="/les-medecines-douces/lhomeopathie/lindividualite">L�individualit�</a></li>
          <li><a class="tree-item" href="/les-medecines-douces/lhomeopathie/la-consultation-homeopathique">La consultation hom�opathique</a></li>
        </ul></li>
    </ul></li>
  <li><a class="tree-item" href="/tout-savoir-sur/reproduction">Reproduction</a></li>
  <li><a class="tree-item" href="/comportement-et-education">Comportement et �ducation</a>
    <ul class="menu tree-admin">
      <li><a class="tree-item" href="/comportement-et-education/comment-bien-vivre-avec-son-chat">Comment bien vivre avec son chat ?</a>
        <ul class="menu tree-admin">
          <li><a class="tree-item" href="/comportement-et-education/comment-bien-vivre-avec-son-chat/le-developpement-comportemental-du-chat">Le d�veloppement comportemental du chat</a></li>
          <li><a class="tree-item" href="/comportement-et-education/comment-bien-vivre-avec-son-chat/les-champs-territoriaux-du-chat">Territoire et communication chez le chat</a></li>
          <li><a class="tree-item" href="/comportement-et-education/comment-bien-vivre-avec-son-chat/les-troubles-du-comportement-du-chat">Les troubles du comportement du chat</a></li>
          <li><a class="tree-item" href="/comportement-et-education/comment-bien-vivre-avec-son-chat/trucs-et-astuces-pour">Trucs et astuces</a>
            <ul class="menu tree-admin">
              <li><a class="tree-item" href="/comportement-et-education/comment-bien-vivre-avec-son-chat/trucs-et-astuces-pour/comment-amenager-le-territoire-de-votre-chat">Pour offrir un
                  territoire apaisant � votre chat</a></li>
              <li><a class="tree-item" href="/comportement-et-education/comment-bien-vivre-avec-son-chat/trucs-et-astuces-pour/pour-empecher-mon-chat-detre-malpropre">Pour emp�cher votre
                  chat d��tre malpropre</a></li>
            </ul></li>
        </ul></li>
    </ul></li>
  <li><a class="tree-item" href="/fiches-pratiques">L�gislation et r�glementation</a>
    <ul class="menu tree-admin">
      <li><a class="tree-item" href="/fiches-pratiques/les-devoirs-dun-bon-maitre">La r�glementation</a></li>
      <li><a class="tree-item" href="/fiches-pratiques/tout-savoir-sur/le-metier-veterinaire">Le monde v�t�rinaire en France</a></li>
      <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/les-soins-apres-une-mise-bas">Les soins apr�s une mise bas</a></li>
      <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/les-maladies-liees-la-reproduction/les-mammites">Les mammites</a></li>
      <li><a class="tree-item" href="/tout-savoir-sur/la-sexualite-de-mon-animal/les-maladies-liees-la-reproduction/leclampsie">L��clampsie</a></li>
    </ul></li>
</ul>
EOT;

$tempname = tempnam(sys_get_temp_dir(), 'restore-menu-') . '.xml';
file_put_contents($tempname, $input);

$nodes = [];

foreach (explode("\n", $reference) as $line) {
    if (!empty($line)) {
        list(,, $alias,,,,,, $nodeId) = explode("\t", $line);
        $nodes[$alias] = $nodeId;
    }
}

$reader = new XMLReader();

if (!$reader->open($tempname)) {
    throw new \Exception("what");
}

$tree = [];
$parent = null;
$previous = null;

$yml = "menu:\n";

while ($reader->read()) {
    switch ($reader->name) {

        case 'ul': // Starts a menu
            break;

        case 'li': // An item
            break;

        case 'a': // Got a link
            $current = $reader->getAttribute('href');
            if ($current !== $previous) {
                for ($i = 0; $i < $reader->depth; ++$i) {
                    $yml .= " ";
                }
                $yml .= ltrim($current, '/') . ":\n";
                $previous = $current;
            }
            break;
    }
}

$parsed = Yaml::parse($yml);

class Item
{
    public $id;
    public $nodeId;
    public $alias;
    public $parent;
    public $children = [];
}

function recurse($array, Item $parent)
{
    global $nodes;

    foreach ($array as $key => $data) {
        $item = null;

        if ($key) {
            $item = new Item();
            $item->alias = $key;

            if (isset($nodes[$key])) {
                $item->nodeId = $nodes[$key];

                if ($parent) {
                    $parent->children[] = $item;
                    $item->parent = $parent;
                }

            } else {
                trigger_error("missing node for alias: " . $key, E_USER_NOTICE);
                $item = null;
            }
        }

        if ($data) {
            if ($item) {
                recurse($data, $item);
            } else {
                $item = recurse($data, $parent);
            }
        }
    }
}

$tree = new Item();
recurse($parsed, $tree);


$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('DRUPAL_ROOT', __DIR__ . '/web');
require_once __DIR__ . '/web/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);


$menuId = 6;
$siteId = 1;

function recurseInTree(Item $item, $weight = 0)
{
    global $menuId;
    global $siteId;

    // id, menu_id, site_id, node_id, parent_id, weight, title, description
    if ($item->nodeId) {
        print $item->nodeId . ': ' . $item->alias . "\n";

        $parentId = $item->parent && $item->parent->id ? $item->parent->id : null;

        if ($node = node_load($item->nodeId)) {
            $title = $node->title;
        } else {
            $title = 'oups, je n\'existe plus';
        }

        $menuItem = [
            'link_path' => 'node/' . $node->nodeId,
            'link_title' => $title,
            'menu_name' => 'site-main-1',
            'weight' => $weight,
            'plid' => $parentId,
        ];

        $item->id = menu_link_save($menuItem);

//         $item->id = db_query("INSERT INTO {umenu_item} (menu_id, site_id, node_id, parent_id, weight) VALUES (:menu_id, :site_id, :node_id, :parent_id, :weight)", [
//             ':menu_id' => $menuId,
//             ':site_id' => $siteId,
//             ':node_id' => $item->nodeId,
//             ':parent_id' => $parentId,
//             ':weight' => $weight,
//         ], ['return' => \Database::RETURN_INSERT_ID]);
    }

    if ($item->children) {
        foreach ($item->children as $i => $child) {
            recurseInTree($child, $i);
        }
    }
}

recurseInTree($tree);
