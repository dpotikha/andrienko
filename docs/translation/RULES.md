# Translation rules — viktor-andrienko.com (Russian → Ukrainian)

The site is the personal site of Ukrainian actor, humorist, screenwriter and TV host **Віктор Андрієнко** (Viktor Andrienko): «Шоу довгоносиків», «Велика різниця», «Іван Сила», cartoons, radio, stage. Most texts are written by him in the first person, in a warm, joking, conversational voice. The site is switching fully to Ukrainian.

## What to produce
- Natural, literary **Ukrainian** (modern standard orthography), not a word-by-word calque. Keep the author's humour, irony, wordplay and tone; where a Russian pun cannot work in Ukrainian, find a Ukrainian equivalent with the same effect.
- Keep the meaning exactly: no additions, no omissions, no summarising, no "improvements" of facts. Dates, numbers, phone numbers, e-mails, URLs stay as they are.
- If a text is **already in Ukrainian**, return it unchanged (except the HTML rules below). If a text mixes languages, translate only the Russian parts. English text stays English.
- Quotations from Russian-language press, interviews and songs are translated too.

## Names
- Use the glossary (`tr/glossary.json`) for every name and title it contains — exactly as written there.
- People: Ukrainian spelling of names (Віктор Андрієнко, Давид Черкаський, Георгій Делієв, Василь Вірастюк). Russian and foreign people: standard Ukrainian transliteration (Сергій Бурунов, Олександр Цекало).
- Titles of films, shows, cartoons, books: the official Ukrainian title if one exists; otherwise translate the title. Put titles in «» quotes.
- Brands and Latin-script names stay as written (SWEET.TV, ICTV, 1+1, Star Media, Firework Sound).

## Titles (job ids starting with `name:`)
- Write in **sentence case**: capital letter only at the start and for proper names and abbreviations. «SWEET.TV ДАРУЄ СВЯТО ДІТЯМ!» → «SWEET.TV дарує свято дітям!»; «МИРОВАЯ ПРЕМЬЕРА "НОМЕРОВ"» → «Світова прем’єра «Номерів»». Titles that are already in normal case keep their case.
- Generic node names: «Фото» → «Фото», «Видео» → «Відео», «Аудио» → «Аудіо».

## HTML (texts containing tags)
- Keep every tag and attribute exactly as it is, in the same order; translate only the human-readable text between tags and the `alt` attribute text. Never change `href`, `src`, numbers in attributes.
- Do not add or remove paragraphs; do not add markdown; do not wrap the result in code fences.
- Typography: Ukrainian apostrophe ’ (U+2019) in words like «п’ять», «прем’єра»; quotes «…»; dash — with spaces.

## Output format
Return a JSON array with one object per input item, same order, same `id`:
`[{"id": "<same id>", "uk": "<translation>"}]`
- Valid JSON (escape quotes and backslashes), UTF-8, every input id present exactly once, nothing else in the file.
