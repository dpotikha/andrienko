# Replace hardcoded Russian admin UI strings with $AdminTrnsl keys and add new keys to the packs.
# Usage (from the site root): python tools/php8-migration/admin_lang_sweep.py [--check]
# Byte-safe: keeps CRLF/LF as is. Each replacement must match the expected number of times,
# otherwise nothing is written (re-running on an already converted tree reports "already done").
import os
import sys

T = lambda k: '<?=$AdminTrnsl["%s"]?>' % k   # HTML / inline-PHP context
P = lambda k: '$AdminTrnsl["%s"]' % k         # PHP context

# file -> list of (old, new, expected_count)
REPL = {
    'admin/logger.php': [
        ("0=>'Вход'", '0=>' + P('Log_Act_Login'), 1),
        ("1=>'Выход'", '1=>' + P('Log_Act_Logout'), 1),
        ("2=>'Изменение'", '2=>' + P('Log_Act_Change'), 1),
        ("3=>'Создание'", '3=>' + P('Log_Act_Create'), 1),
        ("4=>'Удаление'", '4=>' + P('Log_Act_Delete'), 1),
        ('<b>Логгер</b>', '<b>' + T('Logger_Title') + '</b>', 1),
        ('<b>Пользователь</b>', '<b>' + T('Logger_User') + '</b>', 1),
        ('<b>Тип</b>', '<b>' + T('Type') + '</b>', 1),
        ('<b>Деятельность</b>', '<b>' + T('Logger_Activity') + '</b>', 1),
        ('<b>Родительская страница</b>', '<b>' + T('Logger_ParentPage') + '</b>', 1),
        ('<b>Страница</b>', '<b>' + T('Logger_Page') + '</b>', 1),
        ('<b>Дата</b>', '<b>' + T('Date') + '</b>', 1),
        ('>Показать</a>', '>' + T('Generic_Show') + '</a>', 1),
        ('>Скрыть</a>', '>' + T('Generic_Hide') + '</a>', 1),
    ],
    'admin/nodes.php': [
        ('function tags($request) {\n', 'function tags($request) {\n    global $AdminTrnsl;\n', 1),
        ('$s[] = \'Ничего не найдено, нажмите на кнопку "Добавить"\';', '$s[] = ' + P('Tags_NothingFound') + ';', 1),
    ],
    'admin/settings.php': [
        ("alert('Заполните все поля')", "alert('" + T('Msg_FillAllFields') + "')", 1),
        ("alert('Короткие названия повторяются!')", "alert('" + T('Msg_ShortnamesDuplicate') + "')", 1),
        ("> Нет доступа</label>", "> " + T('Rights_None') + "</label>", 2),
        ("> Просмотр</label>", "> " + T('Rights_View') + "</label>", 2),
        ("> Редактирование</label>", "> " + T('Rights_Edit') + "</label>", 2),
        ("confirm('Удалить?')", "confirm('" + T('Confirm_Delete') + "')", 3),
        ('<b>Настройки</b>', '<b>' + T('Menu_Settings') + '</b>', 1),
        ('<b>Короткое название</b>', '<b>' + T('Shortname_Label') + '</b>', 1),
        ('<b>Значение</b>', '<b>' + T('Value') + '</b>', 1),
        ('<b>Доступ</b>', '<b>' + T('Access') + '</b>', 1),
        ('<b>Действие</b>', '<b>' + T('Action') + '</b>', 1),
    ],
    'admin/tree.php': [
        ('onClick="ChangeStyle(this)">Языковые переводы</a>', 'onClick="ChangeStyle(this)">' + T('Menu_Languages') + '</a>', 1),
        ('onClick="ChangeStyle(this)">Почтовая рассылка</a>', 'onClick="ChangeStyle(this)">' + T('Menu_Delivery') + '</a>', 1),
        ('onClick="ChangeStyle(this)">Настройки</a>', 'onClick="ChangeStyle(this)">' + T('Menu_Settings') + '</a>', 1),
    ],
    'admin/classes.php': [
        ('tooltip="Удалить"', 'tooltip="' + T('Delete') + '"', 1),
    ],
    'admin/templates/classes/edit.php': [
        ('<td width=10%>Скрыть от пользователей</td>', '<td width=10%>' + T('Hide_From_Users') + '</td>', 2),
        ('tooltip="Удалить"', 'tooltip="' + T('Delete') + '"', 1),
    ],
    'admin/templates/delivery/index.php': [
        ('<b>Рассылка</b>', '<b>' + T('Post_Management') + '</b>', 1),
    ],
    'admin/templates/fields_controls/serverfilefield.php': [
        ("confirm('Вы действительно хотите удалить ссылку на файл?')", "confirm('" + T('Confirm_DeleteFileLink') + "')", 1),
        ("alert('Файл будет удален после сохранения формы')", "alert('" + T('Msg_FileDeletedAfterSave') + "')", 1),
    ],
    'admin/templates/fields_controls/stinytextfield_normal.php': [
        ('>Включить редактор</a>', '>' + T('Editor_Enable') + '</a>', 1),
    ],
    'admin/templates/fields_controls/stinytextfield_readonly.php': [
        ('>Включить редактор</a>', '>' + T('Editor_Enable') + '</a>', 1),
    ],
    'admin/templates/fields_controls/textareafield_normal.php': [
        ('<b>Дата</b>', '<b>' + T('Date') + '</b>', 1),
        ('class="removeDay" rel="ID">Удалить</a>', 'class="removeDay" rel="ID">' + T('Delete') + '</a>', 1),
        ('>Добавить событие</a>', '>' + T('Program_AddEvent') + '</a>', 1),
        ('<td>Время</td>', '<td>' + T('Program_Time') + '</td>', 1),
        ('<td>Событие (UA)</td>', '<td>' + T('Program_Event_UA') + '</td>', 1),
        ('<td>Событие (EN)</td>', '<td>' + T('Program_Event_EN') + '</td>', 1),
        ('<td>Описание (UA)</td>', '<td>' + T('Program_Desc_UA') + '</td>', 1),
        ('<td>Описание (EN)</td>', '<td>' + T('Program_Desc_EN') + '</td>', 1),
        ('class="removeTime">Удалить</a>', 'class="removeTime">' + T('Delete') + '</a>', 1),
        ('>Добавить день</a>', '>' + T('Program_AddDay') + '</a>', 1),
    ],
    'admin/templates/filelib/file_upload.php': [
        ("'<br />Ошибка: ' + fileObj.name +  '  -  имя файла должно содержать только цифры, латинские буквы, символы \"_-. \". Этот файл не будет загружен.'",
         "'<br />" + T('Upload_ErrorPrefix') + "' + fileObj.name +  '" + T('Upload_BadFilename') + "'", 1),
        ('value="Сохранить">', 'value="' + T('Save') + '">', 1),
    ],
    'admin/templates/mlangs/add_section.php': [
        ('<td>Название секции: </td>', '<td>' + T('Mlang_SectionName') + ' </td>', 1),
        ('<td>Короткое название секции: </td>', '<td>' + T('Mlang_SectionShortname') + ' </td>', 1),
    ],
    'admin/templates/mlangs/edit_section.php': [
        ('<td>Название секции:</td>', '<td>' + T('Mlang_SectionName') + '</td>', 1),
        ('<td>Короткое название секции:</td>', '<td>' + T('Mlang_SectionShortname') + '</td>', 1),
    ],
    'admin/templates/mlangs/index.php': [
        ('<b>Языковые переводы</b>', '<b>' + T('Menu_Languages') + '</b>', 1),
        ("confirm('Вы действительно хотите синхронизировать языковые версии?')", "confirm('" + T('Confirm_SyncVersions') + "')", 1),
        ("confirm('Вы действительно хотите удалить этот перевод?')", "confirm('" + T('Confirm_DeleteTranslation') + "')", 1),
        ("confirm('Вы действительно хотите удалить секцию \"'+section_shortname+'\"?')", "confirm('" + T('Confirm_DeleteSection') + "\"'+section_shortname+'\"?')", 1),
        ("'Создать': function()", "'" + T('Generic_Create') + "': function()", 1),
        ("'Отменить': function()", "'" + T('Generic_Cancel') + "': function()", 2),
        ("'Сохранить': function()", "'" + T('Save') + "': function()", 1),
        ("alert('Выберите переводы для удаления.')", "alert('" + T('Msg_SelectTranslationsToDelete') + "')", 1),
        ("confirm('Вы действительно хотите удалить выбранные переводы? ')", "confirm('" + T('Confirm_DeleteSelectedTranslations') + "')", 1),
    ],
    'admin/templates/mlangs/list.php': [
        ("'Создание секции')\">Добавить секцию</button>", "'" + T('Mlang_CreateSection') + "')\">" + T('Mlang_AddSection') + "</button>", 1),
        ('> Режим сортировки секций', '> ' + T('Mlang_SortMode'), 1),
        ('> Синхронизация языковых версий', '> ' + T('Mlang_SyncVersions'), 1),
        ('>Сохранить сортировку</button>', '>' + T('Mlang_SaveSort') + '</button>', 1),
        ("'Редактрование секции: <?=addslashes", "'" + T('Mlang_EditSectionTitle') + "<?=addslashes", 1),
        ('<b>Короткое название</b>', '<b>' + T('Shortname_Label') + '</b>', 1),
        ('<b>Перевод</b>', '<b>' + T('Mlang_Translation') + '</b>', 1),
        ('<b>Действие</b>', '<b>' + T('Action') + '</b>', 1),
        ('>Добавить перевод в секцию</button>', '>' + T('Mlang_AddTranslation') + '</button>', 1),
        ('>Сохранить языковой файл</button>', '>' + T('Mlang_SaveLangFile') + '</button>', 1),
    ],
    'admin/templates/nodes/addnode.php': [
        ('value="Добавить форму"', 'value="' + T('Node_AddForm') + '"', 1),
        ('value="Удалить форму"', 'value="' + T('Node_DeleteForm') + '"', 1),
        ('>Выбрать</a>', '>' + T('Select') + '</a>', 1),
    ],
    'admin/templates/nodes/editnode.php': [
        ("title='Редактировать'", "title='" + T('Generic_Edit_Full') + "'", 1),
        ("title='Удалить'", "title='" + T('Delete') + "'", 1),
        ('alert("Выберите елементы для переноса")', 'alert("' + T('Msg_SelectItemsToMove') + '")', 1),
        ('alert("Выберите елементы для удаления")', 'alert("' + T('Msg_SelectItemsToDelete') + '")', 1),
        ("confirm('Вы действительно хотите перенести выбранные разделы? Все подразделы будут также перенесены.')", "confirm('" + T('Confirm_MoveSections') + "')", 1),
    ],
    'admin/templates/nodes/editnode_in.php': [
        ("screenY=100')\">Выбрать</a>", "screenY=100')\">" + T('Select') + "</a>", 1),
    ],
    'admin/templates/nodes/multiphoto.php': [
        ('Не заданы настройки загрузки фото-пакетов.', T('Multiphoto_NotConfigured'), 1),
        ("confirm('Вы уверены, что хотите удалить загруженное фото?')", "confirm('" + T('Confirm_DeletePhoto') + "')", 1),
        ("data.filesSelected + ' файлов добавлено в очередь загрузки.'", "data.filesSelected + '" + T('Msg_FilesQueued') + "'", 1),
        ("data.filesUploaded + ' файлов загружено, ' + data.errors + ' ошибок.'", "data.filesUploaded + '" + T('Multiphoto_FilesUploaded') + "' + data.errors + '" + T('Multiphoto_Errors') + "'", 1),
        ('>Выберите файлы на компьютере для загрузки:</div>', '>' + T('Multiphoto_SelectFiles') + '</div>', 1),
    ],
    'admin/templates/nodes/permissions.php': [
        ('<h4>Доступ к разделу</h4>', '<h4>' + T('Perm_AccessToSection') + '</h4>', 1),
        ('<td>Удаление</td>', '<td>' + T('Generic_Deletion') + '</td>', 2),
        ('<h4>Доступ к создаваемым потомкам</h4>', '<h4>' + T('Perm_AccessToChildren') + '</h4>', 1),
        ('> Рекурсивно применить к потомкам <select', '> ' + T('Perm_RecursiveApply') + ' <select', 1),
        ('<option value="0">Всеx групп</option>', '<option value="0">' + T('Perm_AllGroups') + '</option>', 1),
    ],
    'admin/templates/nodes/tags.php': [
        ('>Поиск по первой букве</label>', '>' + T('Tags_SearchFirstLetter') + '</label>', 1),
        ('>Поиск по всему тегу</label>', '>' + T('Tags_SearchWhole') + '</label>', 1),
        ('<option value="0">Выберите категорию</option>', '<option value="0">' + T('Tags_SelectCategory') + '</option>', 1),
        ('value="Добавить" class="btn search__submit"', 'value="' + T('Add') + '" class="btn search__submit"', 1),
        ("{title: 'Тег',", "{title: '" + T('Tags_Tag') + "',", 1),
        ("{title: 'Категория',", "{title: '" + T('Tags_Category') + "',", 1),
        ("{title: 'Действие',", "{title: '" + T('Action') + "',", 1),
        ('confirm("Вы действительно хотите удалить тег?:\\r\\n\\r\\n" + info)', 'confirm("' + T('Tags_ConfirmDelete') + ':\\r\\n\\r\\n" + info)', 1),
        (".attr('alt', 'Удалить')", ".attr('alt', '" + T('Delete') + "')", 1),
        ("alert('Выберите категорию')", "alert('" + T('Tags_SelectCategory') + "')", 1),
    ],
    'admin/templates/nodes/_edit_header.php': [
        ('>Теги</a>', '>' + T('Tags_Tab') + '</a>', 1),
    ],
    'admin/templates/nodes/comments.php': [
        ("confirm('Удалить комментарий?')", "confirm('" + T('Confirm_DeleteComment') + "')", 1),
        ("alert('Введите комментарий')", "alert('" + T('Msg_EnterComment') + "')", 2),
        ("confirm('Заблокировать комментарий?')", "confirm('" + T('Confirm_BlockComment') + "')", 1),
        ("confirm('Разблокировать комментарий?')", "confirm('" + T('Confirm_UnblockComment') + "')", 1),
        ("alert('Введите автора')", "alert('" + T('Msg_EnterAuthor') + "')", 1),
        ("confirm('Удалить выделенные комментарии?')", "confirm('" + T('Confirm_DeleteSelectedComments') + "')", 1),
        ("confirm('Заблокировать выделенные комментарии?')", "confirm('" + T('Confirm_BlockSelectedComments') + "')", 1),
        ("confirm('Разблокировать выделенные комментарии?')", "confirm('" + T('Confirm_UnblockSelectedComments') + "')", 1),
        ('checked="checked" />Дерево</label>', 'checked="checked" />' + T('Comments_ViewTree') + '</label>', 1),
        ('value="list" />Список</label>', 'value="list" />' + T('Comments_ViewList') + '</label>', 1),
        ('value="Добавить комментарий"', 'value="' + T('Comments_AddComment') + '"', 1),
    ],
    'admin/templates/nodes/comments_list.php': [
        ('tooltip="Ответить"', 'tooltip="' + T('Generic_Reply') + '"', 1),
        ('tooltip="Редактировать"', 'tooltip="' + T('Generic_Edit_Full') + '"', 1),
        ('tooltip="Удалить"', 'tooltip="' + T('Delete') + '"', 1),
        ('tooltip="Заблокировать"', 'tooltip="' + T('Generic_Block') + '"', 1),
        ('tooltip="Разблокировать"', 'tooltip="' + T('Generic_Unblock') + '"', 1),
        ('tooltip="Сохранить"', 'tooltip="' + T('Save') + '"', 1),
        ('tooltip="Отменить"', 'tooltip="' + T('Generic_Cancel') + '"', 1),
        ('value="Блокировать"', 'value="' + T('Generic_Block') + '"', 1),
        ('value="Разблокировать"', 'value="' + T('Generic_Unblock') + '"', 1),
        ('value="Удалить" class="button3"', 'value="' + T('Delete') + '" class="button3"', 1),
        ('<h4>Комментариев нет</h4>', '<h4>' + T('Comments_None') + '</h4>', 1),
    ],
    'admin/templates/users/create_group.php': [
        ('<b>Может управлять настройками</b>', '<b>' + T('Group_CanManageSettings') + '</b>', 1),
    ],
    'admin/templates/users/edit_group.php': [
        ('<b>Может управлять настройками</b>', '<b>' + T('Group_CanManageSettings') + '</b>', 1),
    ],
}

# New keys: (key, ua, en). Values: no ASCII ' or " (they may end up in JS strings).
NEW_KEYS = [
    ('Logger_User', 'Користувач', 'User'),
    ('Tags_Tab', 'Теги', 'Tags'),
    ('Tags_NothingFound', 'Нічого не знайдено, натисніть кнопку «Додати»', 'Nothing found, press the «Add» button'),
    ('Tags_SearchFirstLetter', 'Пошук за першою літерою', 'Search by first letter'),
    ('Tags_SearchWhole', 'Пошук за всім тегом', 'Search the whole tag'),
    ('Tags_SelectCategory', 'Виберіть категорію', 'Select a category'),
    ('Tags_Tag', 'Тег', 'Tag'),
    ('Tags_Category', 'Категорія', 'Category'),
    ('Tags_ConfirmDelete', 'Ви дійсно бажаєте видалити тег?', 'Do you really want to delete the tag?'),
    ('Upload_ErrorPrefix', 'Помилка: ', 'Error: '),
    ('Upload_BadFilename', '  -  імʼя файлу може містити лише цифри, латинські літери та символи «_-. ». Цей файл не буде завантажено.',
     '  -  the file name may contain only digits, Latin letters and the characters «_-. ». This file will not be uploaded.'),
    ('Multiphoto_FilesUploaded', ' файлів завантажено, ', ' files uploaded, '),
    ('Multiphoto_Errors', ' помилок.', ' errors.'),
]
PACKS = {'ua': 1, 'en': 2}
PACK_PATH = 'WEB-INF/classes/dbm/messages_%s.properties'
MARK = '// --- Admin UI strings converted from hardcoded text (sweep 2) ---'


def main(check_only):
    out, problems = {}, []
    for f, reps in REPL.items():
        t = open(f, 'rb').read().decode('utf-8')
        for old, new, n in reps:
            c = t.count(old)
            if c != n:
                problems.append('%s: expected %d, found %d: %s' % (f, n, c, old[:70]))
                continue
            t = t.replace(old, new)
        out[f] = t
    for lang, idx in PACKS.items():
        p = PACK_PATH % lang
        t = open(p, 'rb').read().decode('utf-8')
        if MARK in t:
            problems.append('%s: keys already added' % p)
            continue
        for row in NEW_KEYS:
            if '$AdminTrnsl["%s"]' % row[0] in t:
                problems.append('%s: key exists: %s' % (p, row[0]))
        body = ''.join('$AdminTrnsl["%s"] = "%s";\n' % (row[0], row[idx]) for row in NEW_KEYS)
        assert t.endswith('?>')
        out[p] = t[:-2].rstrip('\n') + '\n\n' + MARK + '\n' + body + '?>'
    if problems:
        print('\n'.join(problems))
        print('NOTHING WRITTEN')
        return 1
    if check_only:
        print('check ok: %d files' % len(out))
        return 0
    for p, t in out.items():
        open(p, 'wb').write(t.encode('utf-8'))
    print('written: %d files' % len(out))
    return 0


if __name__ == '__main__':
    sys.exit(main('--check' in sys.argv))
