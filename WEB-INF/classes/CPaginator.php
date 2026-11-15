<?php

Class CPaginator {

    public
    $pagesInPaginator,
    $totalPages,
    $totalRecords,
    $currentPage,
    $parentNode,
    $limitPerPage,
    $offset,
    $values,
    $startPage,
    $endPage,
    $previousPage,
    $nextPage,
    $getParameters;

    public function __construct($parentNode, $limitPerPage = 10, $pagesInPaginator = 10, $order = 'n.subtree_order', $class='', $filter_params=array()) {
        $this->limitPerPage = $limitPerPage;
        $this->parentNode = $parentNode;
        $this->pagesInPaginator = $pagesInPaginator;
        $this->currentPage = isset($_GET['page']) ? abs((int) $_GET['page']) : (isset($_POST['page']) ? abs((int) $_POST['page']) : 1);
        if ($class == '') {
            $this->totalRecords = $this->parentNode->getChildrenCount();
        } else {
            $this->totalRecords = $this->parentNode->getChildrenCountOfClassWithFilter($class, $filter_params);
        }
        $this->totalPages = ceil($this->totalRecords / $this->limitPerPage);
        if ($this->currentPage > $this->totalPages)
            $this->currentPage = $this->totalPages;
        if ($this->currentPage < 1)
            $this->currentPage = 1;
        $this->offset = ($this->currentPage - 1) * $this->limitPerPage;
        $dec = floor(($this->currentPage - 1) / $this->pagesInPaginator);
        $this->startPage = $dec * $this->pagesInPaginator + 1;
        $this->endPage = ($dec + 1) * $this->pagesInPaginator;
        if ($this->totalPages < $this->endPage)
            $this->endPage = $this->totalPages;
        if ($this->currentPage < $this->totalPages)
            $this->nextPage = $this->currentPage + 1;
        if ($this->currentPage > 1)
            $this->previousPage = $this->currentPage - 1;
        if ($class == '') {
            $this->values = $this->parentNode->getChildren($order, $this->offset, $this->limitPerPage);
        } else {
            $this->values = $this->parentNode->getChildrenOfClassWithFilter($class, $order, $this->offset, $this->limitPerPage, $filter_params);
        }
    }

}

Class CPaginatorByQuery {

    public
    $pagesInPaginator,
    $totalPages,
    $totalRecords,
    $currentPage,
    $limitPerPage,
    $offset,
    $values,
    $startPage,
    $endPage,
    $previousPage,
    $nextPage,
    $getParameters;

    public function __construct($queryForTotalRecords, $queryForValues, $limitPerPage = 10, $pagesInPaginator = 10) {
        global $MDB, $request;
        $MDB->Query($queryForTotalRecords);
        $res = $MDB->NextResult();
        $this->totalRecords = $res ? $res['count'] : 0;

        $this->limitPerPage = $limitPerPage;
        $this->pagesInPaginator = $pagesInPaginator;
        $this->currentPage = isset($_GET['page']) ? abs((int) $_GET['page']) : (isset($_POST['page']) ? abs((int) $_POST['page']) : 1);
        $this->totalPages = ceil($this->totalRecords / $this->limitPerPage);
        if ($this->currentPage > $this->totalPages)
            $this->currentPage = $this->totalPages;
        if ($this->currentPage < 1)
            $this->currentPage = 1;
        $this->offset = ($this->currentPage - 1) * $this->limitPerPage;
        $dec = floor(($this->currentPage - 1) / $this->pagesInPaginator);
        $this->startPage = $dec * $this->pagesInPaginator + 1;
        $this->endPage = ($dec + 1) * $this->pagesInPaginator;
        
        if ($this->currentPage < $this->totalPages)
            $this->nextPage = $this->currentPage + 1;
        if ($this->currentPage > 1)
            $this->previousPage = $this->currentPage - 1;
	
        $this->endPage=$this->currentPage+ceil($pagesInPaginator/2);
        $this->startPage=$this->currentPage- ceil($pagesInPaginator/2);
        if ($this->totalPages < $this->endPage)
            $this->endPage = $this->totalPages;
        if ( $this->startPage < 1)
             $this->startPage = 1;
        
                
        if ($this->endPage - $this->startPage < $pagesInPaginator && $this->startPage>=$pagesInPaginator) {
            $this->startPage = $this->endPage - $pagesInPaginator;
            if ($this->startPage < 1)
                $this->startPage = 1;
        }elseif ($this->endPage - $this->startPage < $pagesInPaginator && $this->startPage<=$pagesInPaginator) {
            $this->endPage = $this->startPage + $pagesInPaginator;
            if ($this->endPage >$this->totalPages )
                $this->endPage = $this->totalPages;
        }	

        $MDB->Query(preg_replace(array('{{#offset#}}', '{{#limit#}}'), array($this->offset, $this->limitPerPage), $queryForValues));
        $this->values = $MDB->getRecords();
    }

}

Class CPaginatorForSearch {

    public
    $pagesInPaginator,
    $totalPages,
    $totalRecords,
    $currentPage,
    $limitPerPage,
    $offset,
    $values,
    $startPage,
    $endPage,
    $previousPage,
    $nextPage,
    $getParameters;

    public function __construct($queryForTotalRecords, $queryForValues, $limitPerPage = 10, $pagesInPaginator = 10) {
        $this->totalRecords = Node::findCountByQuery($queryForTotalRecords); //$res ? $res['count'] : 0;
        $this->limitPerPage = $limitPerPage;
        $this->pagesInPaginator = $pagesInPaginator;
        $this->currentPage = isset($_GET['page']) ? abs((int) $_GET['page']) : 1;
        $this->totalPages = ceil($this->totalRecords / $this->limitPerPage);
        if ($this->currentPage > $this->totalPages)
            $this->currentPage = $this->totalPages;
        if ($this->currentPage < 1)
            $this->currentPage = 1;
        $this->offset = ($this->currentPage - 1) * $this->limitPerPage;
        $dec = floor(($this->currentPage - 1) / $this->pagesInPaginator);
        $this->startPage = $dec * $this->pagesInPaginator + 1;
        $this->endPage = ($dec + 1) * $this->pagesInPaginator;
        if ($this->totalPages < $this->endPage)
            $this->endPage = $this->totalPages;
        if ($this->currentPage < $this->totalPages)
            $this->nextPage = $this->currentPage + 1;
        if ($this->currentPage > 1)
            $this->previousPage = $this->currentPage - 1;
        $queryForValues = preg_replace(array('{{#offset#}}', '{{#limit#}}'), array($this->offset, $this->limitPerPage), $queryForValues);
        $this->values = Node::findByQuery($queryForValues);
    }

}

Class CPaginatorByArray {

    public
    $pagesInPaginator,
    $totalPages,
    $totalRecords,
    $currentPage,
    $limitPerPage,
    $offset,
    $values,
    $startPage,
    $endPage,
    $previousPage,
    $nextPage,
    $arr,
    $getParameters;

    public function __construct(&$arr, $limitPerPage = 10, $pagesInPaginator = 10) {
        $this->arr = $arr;
        $this->limitPerPage = $limitPerPage;
        $this->pagesInPaginator = $pagesInPaginator;
        $this->currentPage = isset($_GET['page']) ? abs((int) $_GET['page']) : (isset($_POST['page']) ? abs((int) $_POST['page']) : 1);
        $this->totalRecords = sizeof($this->arr);
        $this->totalPages = ceil($this->totalRecords / $this->limitPerPage);
        if ($this->currentPage > $this->totalPages)
            $this->currentPage = $this->totalPages;
        if ($this->currentPage < 1)
            $this->currentPage = 1;
        $this->offset = ($this->currentPage - 1) * $this->limitPerPage;
        $dec = floor(($this->currentPage - 1) / $this->pagesInPaginator);
        $this->startPage = $dec * $this->pagesInPaginator + 1;
        $this->endPage = ($dec + 1) * $this->pagesInPaginator;
        if ($this->totalPages < $this->endPage)
            $this->endPage = $this->totalPages;
        if ($this->currentPage < $this->totalPages)
            $this->nextPage = $this->currentPage + 1;
        if ($this->currentPage > 1)
            $this->previousPage = $this->currentPage - 1;
        $this->values = array_slice($this->arr, $this->offset, $this->limitPerPage);
    }

}


Class CPaginatorForMegaSearch {

    public
    $pagesInPaginator,
    $totalPages,
    $totalRecords,
    $currentPage,
    $limitPerPage,
    $offset,
    $values,
    $startPage,
    $endPage,
    $previousPage,
    $nextPage,
    $getParameters,

    $keyword,
    $dbmTargets,
    $tableTargets,
    $hiddenNodes,
    $forbiddenRoots,
    
    $shownFrom,
    $shownTo;

    public function __construct($hiddenNodes = array(), $forbiddenRoots = array(), $limitPerPage = 10, $pagesInPaginator = 10) {
        $this->dbmTargets = array();
        $this->tableTargets = array();
        $this->limitPerPage = $limitPerPage;
        $this->pagesInPaginator = $pagesInPaginator;
        $this->hiddenNodes = $hiddenNodes;
        $this->forbiddenRoots = $forbiddenRoots;
        $keyword = stripslashes(trim(urldecode((isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : '')))));
        $this->keyword = db_escape(preg_replace(array("/'/", "/’/"), array('&rsquo;', '&rsquo;'), $keyword));
        $this->titleField = $titleField;
        $this->currentPage = isset($_GET['page']) ? abs((int) $_GET['page']) : (isset($_POST['page']) ? abs((int) $_POST['page']) : 1);
    }

    public function addDbmClass($className, $fields, $titleField = 'name', $where = '') {
        if (strlen($where))
            $where = " $where AND ";
        $this->dbmTargets[$className] = array('className' => $className, 'fields' => $fields, 'titleField' => $titleField, 'where' => $where);
    }
    public function addTable($tableName, $fields, $titleField = 'name', $where = '') {
        if (strlen($where))
            $where = " $where AND ";
        $this->tableTargets[$tableName] = array('tableName' => $tableName, 'fields' => $fields, 'titleField' => $titleField, 'where' => $where);
    }


    public function go() {
//        if (!strlen($this->keyword)) {
//            $this->values = null;
//            return;
//        }
        if (sizeof($this->dbmTargets) + sizeof($this->tableTargets) == 0) return;
        global $MDB, $request;
        
        $like = " LIKE '%" . $this->keyword . "%' ";
        $not_in = (sizeof($this->forbiddenRoots) ? " AND n.parent_id NOT IN(" . implode(',', $this->forbiddenRoots) . ") " : '') . (sizeof($this->hiddenNodes) ? " AND n.id NOT IN(" . implode(',', $this->hiddenNodes) . ") " : '');
        if (sizeof($this->dbmTargets))
            foreach ($this->dbmTargets as $k => $v) {
                $resultForTotalRecords[] = "_$k.count";
                $tablesForTotalRecords[] = "(SELECT count(id) as count FROM dbm_nodes n, dbm_nfv_{$v['className']} d
                    WHERE n.id = d.node_id AND " . $v['where'] . " (" . implode(" $like OR ", $v['fields']) . "$like) $not_in) _$k";

                $tablesForValues[] = "(SELECT n.id, '{$v['className']}' descr, 'class' type FROM dbm_nodes n, dbm_nfv_{$v['className']} d
                    WHERE n.id = d.node_id AND " . $v['where'] . " (" . implode(" $like OR ", $v['fields']) . "$like) $not_in)";
//                $tablesForTotalRecords[] = "(SELECT count(id) as count FROM dbm_nodes n, dbm_nfv_{$v['className']} d
//                    WHERE n.id = d.node_id AND " . $v['where'] . " (" . implode(" $like OR ", $v['fields']) . "$like)
//                        $not_in) _$k";
//
//                $tablesForValues[] = "(SELECT n.id, '{$v['className']}' descr, 'class' type FROM dbm_nodes n, dbm_nfv_{$v['className']} d
//                    WHERE n.id = d.node_id AND " . $v['where'] . " (" . implode(" $like OR ", $v['fields']) . "$like)
//                        AND n.parent_id NOT IN(" . implode(',', $this->forbiddenRoots) . ")
//                        AND n.id NOT IN(" . implode(',', $this->hiddenNodes) . "))";
            }
        if (sizeof($this->tableTargets))
            foreach ($this->tableTargets as $k => $v) {
                $k += sizeof($this->dbmTargets);
                $resultForTotalRecords[] = "_$k.count";
                $tablesForTotalRecords[] = "(SELECT count(id) as count FROM {$v['tableName']} WHERE " . $v['where'] . " (" . implode(" $like OR ", $v['fields']) . "$like)) _$k";
                $tablesForValues[] = "(SELECT id, '{$v['tableName']}' descr, 'table' type FROM {$v['tableName']} WHERE " . $v['where'] . " (" . implode(" $like OR ", $v['fields']) . "$like))";
            }
        $queryForTotalRecords = "SELECT " . implode(' + ', $resultForTotalRecords) . " as count
            FROM " . implode(' , ', $tablesForTotalRecords);
        $queryForValues = implode(' UNION ', $tablesForValues) . " LIMIT {#offset#}, {#limit#}";

        $MDB->Query($queryForTotalRecords);
        $res = $MDB->NextResult();
        $this->totalRecords = $res ? $res['count'] : 0;

        $this->totalPages = ceil($this->totalRecords / $this->limitPerPage);
        if ($this->currentPage > $this->totalPages)
            $this->currentPage = $this->totalPages;
        if ($this->currentPage < 1)
            $this->currentPage = 1;
        $this->offset = ($this->currentPage - 1) * $this->limitPerPage;
        $dec = floor(($this->currentPage - 1) / $this->pagesInPaginator);
        $this->startPage = $dec * $this->pagesInPaginator + 1;
        $this->endPage = ($dec + 1) * $this->pagesInPaginator;
        if ($this->totalPages < $this->endPage)
            $this->endPage = $this->totalPages;
        if ($this->currentPage < $this->totalPages)
            $this->nextPage = $this->currentPage + 1;
        if ($this->currentPage > 1)
            $this->previousPage = $this->currentPage - 1;
        
        $this->shownFrom = $this->totalRecords ? $this->offset + 1 : 0;
        $this->shownTo = min($this->totalRecords, $this->offset + $this->limitPerPage);

        $queryForValues = preg_replace(array('{{#offset#}}', '{{#limit#}}'), array($this->offset, $this->limitPerPage), $queryForValues);
        $MDB->Query($queryForValues);
        $this->values = $MDB->getRecords();

        
        if ($this->values)
            foreach ($this->values as $k => $v) {
                if ($v['type'] == 'class') {
                    $node = QNode::getInstance($v['id']);
                    $this->values[$k]['node'] = $node;
                    $this->values[$k]['type'] = 'dbm';
                    $this->values[$k]['shortText'] = $this->getShortTextForDbmClass($node, $this->dbmTargets[$v['descr']]['fields']);
                    $this->values[$k]['title'] = strlen($this->dbmTargets[$v['descr']]['titleField']) ? ($this->dbmTargets[$v['descr']]['titleField'] == 'name' ? $this->highlight($node->name) : $this->highlight($node->tfields[$this->dbmTargets[$v['descr']]['titleField']])) : '';
                } else { 
                    $MDB->Query("SELECT * FROM {$v['descr']} WHERE id = {$v['id']}");
                    $res = $MDB->NextResult();
                    $this->values[$k]['data'] = $res;
                    $this->values[$k]['type'] = 'table';
                    $this->values[$k]['shortText'] = $this->getShortTextForTable($res, $this->tableTargets[$v['descr']]['fields']);
                    if (strlen($this->tableTargets[$v['descr']]['titleField']))
                        $this->values[$k]['title'] = $this->highlight($res[$this->tableTargets[$v['descr']]['titleField']]);
                    else $this->values[$k]['title'] = '';
                }
            }
    }

    private function getShortTextForDbmClass(&$node, $fields) {
        $field = $fields[0];
        foreach ($fields as $k => $v) {
            if (strlen($this->keyword) && (mb_substr_count($node->tfields[$v], $this->keyword) > 0 || mb_substr_count($node->tfields[$v], $this->my_ucfirst($this->keyword)) > 0)) {
                $field = $v;
                break;
            }
        }
        $highlited = $this->highlight($node->tfields[$field]);
        return $highlited .  ((mb_strlen($node->tfields[$field]) > mb_strlen($highlited)) ? '...' : '');
    }

    private function getShortTextForTable($res, $fields) {
        $field = $fields[0];
        foreach ($fields as $k => $v) {
            if (strlen($this->keyword) && (mb_substr_count($res[$v], $this->keyword) > 0 || mb_substr_count($res[$v], $this->my_ucfirst($this->keyword)) > 0)) {
                $field = $v;
                break;
            }
        }
        $highlited = $this->highlight($res[$field]);
        return $highlited .  ((mb_strlen($res[$field]) > mb_strlen($highlited)) ? '...' : '');
    }

    private function highlight($text, $length = 512) {
        $text = CProtect::string($text, $length);
        //return preg_replace(array('/' . $keyword . '/', '/' . my_ucfirst($keyword) . '/', '/' . mb_strtolower($keyword) . '/'), array('<span class="foundWord">' . $keyword . '</span>', '<span class="foundWord">' . my_ucfirst($keyword) . '</span>', '<span class="foundWord">' . mb_strtolower($keyword) . '</span>'), $str) . ((mb_strlen($node->tfields[$field]) > mb_strlen($str)) ? '...' : '');
        return preg_replace(array('/' . mb_strtolower($this->keyword) . '/i'), array('<span class="searchWord">' . mb_strtolower($this->keyword) . '</span>'), $text);
    }

    private function my_ucfirst($string, $e ='utf-8') {
        if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
            $string = mb_strtolower($string, $e);
            $upper = mb_strtoupper($string, $e);
            preg_match('#(.)#us', $upper, $matches);
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e);
        } else {
            $string = ucfirst($string);
        }
        return $string;
    }

}
?>