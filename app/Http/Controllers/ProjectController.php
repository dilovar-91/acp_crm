<?php

namespace App\Http\Controllers;

use App\Models\ActualProject;
use App\Models\ProjectManager;
use App\Models\ProjectStatus;
use App\Models\ProjectSystem;
use App\Models\ProjectType;
use App\Models\SiteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProjectController extends Controller
{
    public function projects(): JsonResponse
    {

        $projects = QueryBuilder::for(ActualProject::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::exact('manager_id'), AllowedFilter::exact('site_id'), AllowedFilter::exact('status_id'), AllowedFilter::exact('manager_id'), AllowedFilter::exact('type_id'), AllowedFilter::exact('system_id')])->with(['showroom', 'site', 'status','manager', 'type', 'system'])->sortByShowroom([4, 2, 10, 5, 7,8, 11,1,9])->get();
        return response()->json($projects, 200);
    }

    public function managers(): JsonResponse
    {
        $managers = ProjectManager::get();
        return response()->json($managers, 200);
    }

    public function statuses (): JsonResponse
    {
        $items = ProjectStatus::get();
        return response()->json($items, 200);
    }

    public function types (): JsonResponse
    {
        $items = ProjectType::get();
        return response()->json($items, 200);
    }

    /**
     * Метод для получения системы проекта.
     *
     * @return JsonResponse JSON-ответ с системы проекта или пустой массив.
     *
     */

    public function systems(): JsonResponse
    {
        $items = ProjectSystem::get();
        return response()->json($items, 200);
    }



    /**
     * Метод для создания или обновления проекта.
     *
     * @param Request $request HTTP-запрос, содержащий данные для создания или обновления проекта.
     *
     * @return JsonResponse JSON-ответ с обновленным или созданным проектом, либо с ошибкой.
     *
     * @throws ValidationException Если валидация данных не прошла.
     * @throws ModelNotFoundException Если проект не найден при обновлении.
     */
    public function storeOrUpdate(Request $request)
    {
        /*
        $request->validate([
            'item.site_id' => 'required',
            'item.showroom_id' => 'required',
            'item.type_id' => 'nullable|number',
            'item.manager_id' => 'nullable|number',
            'item.system_id' => 'nullable|number',
            'item.status_id' => 'nullable|number',
            'item.comment' => 'nullable|string',
        ]);
        */

        $data = [
            'site_id' => $request->item['site_id'],
            'showroom_id' => $request->item['showroom_id'],
            'type_id' => $request->item['type_id'],
            'manager_id' => $request->item['manager_id'],
            'system_id' => $request->item['system_id'],
            'status_id' => $request->item['status_id'],
            'comment' => $request->item['comment']
        ];



        $id = $request->item['id'];

        if ($id) {
            //return $data;
            $project = ActualProject::find($id);
            if (!$project) {
                return response()->json(['error' => 'Проект не найден'], 404);
            }
            $project->update($data);
            return response()->json($project);
        } else {
            $project = ActualProject::create($data);
            return response()->json($project);
        }
    }
}
