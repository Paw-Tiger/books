<?php

class Books
{
    private static $table = 'books';

    public function __construct(Mysqli $db, $data)
    {
        $result = null;
        $keys = array_keys($data);
        $values = array_values($data);

        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (\'%s\')',
            self::$table,
            implode(', ', $keys),
            implode('\', \'', $values)
        );

        $resultQuery = $db->query($query);

        if ($resultQuery) {
            $result = $db->insert_id;
        }
        return $result;
    }

    public static function find(Mysqli $db, $id = NULL)
    {
        $result = NULL;

        if ($id!==NULL) {

            $query = sprintf('SELECT * FROM %s WHERE id=%d', self::$table, $id);

            $resultQuery = $db->query($query);

            $result = $resultQuery->fetch_assoc();
        } else {
            $query = sprintf('SELECT * FROM %s', self::$table);

            $resultQuery = $db->query($query);
            while ($res = $resultQuery->fetch_assoc()) {
                $result[] = $res;
            }

        }

        return $result;
    }

    public static function update(Mysqli $db, $data, $condition, $comparator = 'AND')
    {
        $result = null;
        $keys = array_keys($data);
        $values = array_values($data);

        $keysCondition = array_keys($condition);
        $valuesCondition = array_values($condition);

        $dataStr = array_map(
            function ($key, $value) {
                return $key . " = '" . $value . "'";
            },
            $keys,
            $values
        );

        $conditionStr = array_map(
            function ($key, $value) {
                return $key . " = '" . $value . "'";
            },
            $keysCondition,
            $valuesCondition
        );


        $comparator = " " . strtoupper($comparator) . " ";
        $query = sprintf(
            'UPDATE %s SET %s WHERE %s',
            self::$table,
            implode(', ', $dataStr),
            implode($comparator, $conditionStr)
        );

        $resultQuery = $db->query($query);

        if ($resultQuery) {
            $result = true;
        }
        return $result;

    }

    /**
     * @param Mysqli $db
     * @param $id
     * @return bool|mysqli_result
     */
    public static function delete(Mysqli $db, $id)
    {
        $queryStr = sprintf('DELETE FROM %s WHERE id= ?', self::$table);
        $query = $db->prepare($queryStr);
        $query->bind_param('i', $id);
        $query->execute();
        return true;
    }

}