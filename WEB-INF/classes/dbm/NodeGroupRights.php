<?php

DEFINE("VIEW_RIGHT_MASK", 0x01);
DEFINE("WRITE_RIGHT_MASK", 0x02);
DEFINE("DELETE_RIGHT_MASK", 0x04);
DEFINE("VIEW_RIGHT_FOR_CHILDREN_MASK", 0x08);
DEFINE("WRITE_RIGHT_FOR_CHILDREN_MASK", 0x10);
DEFINE("DELETE_RIGHT_FOR_CHILDREN_MASK", 0x20);
DEFINE("NOT_VIEW_RIGHT_MASK", 0x7E);
DEFINE("NOT_WRITE_RIGHT_MASK", 0x7D);
DEFINE("NOT_DELETE_RIGHT_MASK", 0x7B);
DEFINE("NOT_VIEW_RIGHT_FOR_CHILDREN_MASK", 0x77);
DEFINE("NOT_WRITE_RIGHT_FOR_CHILDREN_MASK", 0x6F);
DEFINE("NOT_DELETE_RIGHT_FOR_CHILDREN_MASK", 0x5F);

class NodeGroupRights {
    var $rights = 0;

    function hasViewRight() {
        return ($this->rights & VIEW_RIGHT_MASK) != 0;
    }
    function getViewRight() {
    	return $this->hasViewRight();
    }

    function hasWriteRight() {
        return ($this->rights & WRITE_RIGHT_MASK) != 0;
    }
    function hasDeleteRight() {
		return ($this->rights & DELETE_RIGHT_MASK) != 0;
    }
    function getWriteRight() {
        return $this->hasWriteRight();
    }
    function getDeleteRight() {
        return $this->hasDeleteRight();
    }

    function setViewRight() {
        $this->rights |= VIEW_RIGHT_MASK;
    }
    function setWriteRight() {
        $this->rights |= WRITE_RIGHT_MASK;
    }
    function setDeleteRight() {
        $this->rights |= DELETE_RIGHT_MASK;
    }
    function unsetDeleteRight() {
        $this->rights &= NOT_DELETE_RIGHT_MASK;
    }

    function unsetViewRight() {
        $this->rights &= NOT_VIEW_RIGHT_MASK;
    }
    function unsetWriteRight() {
        $this->rights &= NOT_WRITE_RIGHT_MASK;
    }

    //--------------------
    function hasViewRightForChildren() {
        return ($this->rights & VIEW_RIGHT_FOR_CHILDREN_MASK) != 0;
    }
    function getViewRightForChildren() {
    	return $this->hasViewRightForChildren();
    }

    function hasWriteRightForChildren() {
        return ($this->rights & WRITE_RIGHT_FOR_CHILDREN_MASK) != 0;
    }
    function hasDeleteRightForChildren() {
		return ($this->rights & DELETE_RIGHT_FOR_CHILDREN_MASK) != 0;
    }
    function getWriteRightForChildren() {
        return $this->hasWriteRightForChildren();
    }
    function getDeleteRightForChildren() {
        return $this->hasDeleteRightForChildren();
    }

    function setViewRightForChildren() {
        $this->rights |= VIEW_RIGHT_FOR_CHILDREN_MASK;
    }
    function setWriteRightForChildren() {
        $this->rights |= WRITE_RIGHT_FOR_CHILDREN_MASK;
    }
    function setDeleteRightForChildren() {
        $this->rights |= DELETE_RIGHT_FOR_CHILDREN_MASK;
    }
    function unsetDeleteRightForChildren() {
        $this->rights &= NOT_DELETE_RIGHT_FOR_CHILDREN_MASK;
    }

    function unsetViewRightForChildren() {
        $this->rights &= NOT_VIEW_RIGHT_FOR_CHILDREN_MASK;
    }
    function unsetWriteRightForChildren() {
        $this->rights &= NOT_WRITE_RIGHT_FOR_CHILDREN_MASK;
    }
    //--------------------

    var $group = null;
    var $node = null;

    function getRights() {
        return $this->rights;
    }
    function getRightsForChildren() {
        $t = 0;
        if ($this->hasViewRightForChildren()) {
            $t |= VIEW_RIGHT_MASK;
            $t |= VIEW_RIGHT_FOR_CHILDREN_MASK;
        }
        if ($this->hasWriteRightForChildren()) {
            $t |= WRITE_RIGHT_MASK;
            $t |= WRITE_RIGHT_FOR_CHILDREN_MASK;
        }
        if ($this->hasDeleteRightForChildren()) {
            $t |= DELETE_RIGHT_MASK;
            $t |= DELETE_RIGHT_FOR_CHILDREN_MASK;
        }
        return $t;
    }
    function setRights($rights) {
        $this->rights = $rights ;
    }


    function __construct($cgroup, $cnode) {
        $this->group = $cgroup;
        $this->node = $cnode;

        if (is_int($this->node))
        	$node_id = $this->node;
        else
        	$node_id = $this->node->id;
        $query = "SELECT rights FROM dbm_group_rights WHERE group_id=" . $this->group->id . " and node_id=" . $node_id;
        $res = DBUtils::execSelect($query);

        $this->rights = 0;
        if ($res->next()) {
            $this->rights = $res->getInt("rights");
        } else {
        	$this->store();
        }
    }

    function store() {
        if (is_int($this->node))
        	$node_id = $this->node;
        else
        	$node_id = $this->node->id;
        $sql = "REPLACE INTO dbm_group_rights(rights,group_id,node_id) VALUES (?,?,?)";
        DBUtils::execUpdate($sql, array($this->rights, $this->group->id, $node_id));
    }
}

?>