<?php namespace modules\finance\components;

// "Keep the essence of your code, code isn't just a code, it's an art." -- Rifan Firdhaus Widigdo
use modules\finance\models\Expense;
use modules\finance\widgets\inputs\ExpenseInput;
use modules\task\components\TaskRelation;

/**
 * @author Rifan Firdhaus Widigdo <rifanfirdhaus@gmail.com>
 *
 * @property Expense $model
 */
class ExpenseNoteRelation extends TaskRelation
{
    use ExpenseRelatedTrait;
}