<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class EmployeeNovedadesTable extends Table
{
    public const NOVEDAD_TYPES = [
        'En Licencia',
        'En Vacaciones',
        'En Permiso',
        'Compensado',
    ];

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('employee_novedades');
        $this->setDisplayField('novedad_type');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Employees', [
            'foreignKey' => 'employee_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CreatedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('novedad_type')
            ->inList('novedad_type', self::NOVEDAD_TYPES, 'Tipo de novedad inválido.')
            ->requirePresence('novedad_type', 'create')
            ->notEmptyString('novedad_type');

        $validator
            ->date('start_date')
            ->requirePresence('start_date', 'create')
            ->notEmptyDate('start_date');

        $validator
            ->date('end_date')
            ->allowEmptyDate('end_date');

        $validator
            ->scalar('observations')
            ->allowEmptyString('observations');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('employee_id', 'Employees'), ['errorField' => 'employee_id']);
        $rules->add($rules->existsIn('created_by', 'CreatedByUsers'), ['errorField' => 'created_by', 'allowNullableNulls' => true]);

        // Only one active novedad per employee
        $rules->add(function ($entity) {
            if (!$entity->active) {
                return true;
            }
            $count = $this->find()
                ->where([
                    'employee_id' => $entity->employee_id,
                    'active' => true,
                ]);
            if (!$entity->isNew()) {
                $count = $count->where(['id !=' => $entity->id]);
            }

            return $count->count() === 0;
        }, 'oneActivePerEmployee', [
            'errorField' => 'active',
            'message' => 'El empleado ya tiene una novedad activa. Desactívela primero.',
        ]);

        return $rules;
    }
}
