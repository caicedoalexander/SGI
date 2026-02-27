<?php
declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class EmployeeLeavesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('employee_leaves');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Employees', [
            'foreignKey' => 'employee_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('LeaveTypes', [
            'foreignKey' => 'leave_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ApprovedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'approved_by',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('RequestedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'requested_by',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('employee_id')
            ->requirePresence('employee_id', 'create')
            ->notEmptyString('employee_id');

        $validator
            ->integer('leave_type_id')
            ->requirePresence('leave_type_id', 'create')
            ->notEmptyString('leave_type_id');

        $validator
            ->date('start_date')
            ->requirePresence('start_date', 'create')
            ->notEmptyDate('start_date');

        $validator
            ->date('end_date')
            ->requirePresence('end_date', 'create')
            ->notEmptyDate('end_date');

        $validator
            ->scalar('status')
            ->inList('status', ['pendiente', 'aprobado', 'rechazado']);

        $validator
            ->scalar('observations')
            ->allowEmptyString('observations');

        $validator
            ->date('fecha_permiso')
            ->allowEmptyDate('fecha_permiso');

        $validator
            ->date('fecha_diligenciamiento')
            ->allowEmptyDate('fecha_diligenciamiento');

        $validator
            ->scalar('horario')
            ->inList('horario', ['Por horas', 'Por días'])
            ->allowEmptyString('horario');

        $validator
            ->time('hora_salida')
            ->allowEmptyTime('hora_salida');

        $validator
            ->time('hora_entrada')
            ->allowEmptyTime('hora_entrada');

        $validator
            ->integer('cantidad_dias')
            ->allowEmptyString('cantidad_dias');

        $validator
            ->boolean('remunerado')
            ->allowEmptyString('remunerado');

        return $validator;
    }

    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
    {
        $horario = $data['horario'] ?? null;

        // Clean irrelevant fields based on horario selection
        if ($horario === 'Por horas') {
            $data['cantidad_dias'] = null;
        } elseif ($horario === 'Por días') {
            $data['hora_salida'] = null;
            $data['hora_entrada'] = null;
        }
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('employee_id', 'Employees'), ['errorField' => 'employee_id']);
        $rules->add($rules->existsIn('leave_type_id', 'LeaveTypes'), ['errorField' => 'leave_type_id']);
        $rules->add($rules->existsIn('approved_by', 'ApprovedByUsers'), [
            'errorField' => 'approved_by',
            'allowNullableNulls' => true,
        ]);
        $rules->add($rules->existsIn('requested_by', 'RequestedByUsers'), ['errorField' => 'requested_by']);

        // Conditional: if horario='Por horas', require hora_salida and hora_entrada
        $rules->add(function ($entity) {
            if ($entity->horario !== 'Por horas') {
                return true;
            }

            return !empty($entity->hora_salida) && !empty($entity->hora_entrada);
        }, 'horasRequired', [
            'errorField' => 'hora_salida',
            'message' => 'Hora de salida y entrada son requeridas para horario "Por horas".',
        ]);

        // Conditional: hora_salida < hora_entrada
        $rules->add(function ($entity) {
            if ($entity->horario !== 'Por horas' || empty($entity->hora_salida) || empty($entity->hora_entrada)) {
                return true;
            }

            return (string)$entity->hora_salida < (string)$entity->hora_entrada;
        }, 'horaSalidaBeforeEntrada', [
            'errorField' => 'hora_salida',
            'message' => 'La hora de salida debe ser anterior a la hora de entrada.',
        ]);

        // Conditional: if horario='Por días', require cantidad_dias > 0
        $rules->add(function ($entity) {
            if ($entity->horario !== 'Por días') {
                return true;
            }

            return !empty($entity->cantidad_dias) && $entity->cantidad_dias > 0;
        }, 'cantidadDiasRequired', [
            'errorField' => 'cantidad_dias',
            'message' => 'La cantidad de días es requerida y debe ser mayor a 0.',
        ]);

        return $rules;
    }
}
