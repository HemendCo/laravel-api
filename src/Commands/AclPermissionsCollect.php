<?php

namespace Hemend\Api\Commands;

use App\Models\AclPermissions as Permissions;
use App\Models\AclGroups;
use Hemend\Library\Strings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class AclPermissionsCollect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:acl-collect
            {service : The Service name/namespace of the api service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $validator = Validator::make($this->arguments(), [
            'service' => 'bail|required|string|min:1|max:150|regex:/^[A-Z][a-zA-Z0-9\\\]+$/'
        ]);

        if($validator->fails()) {
            $this->error('The service name/namespace "'.$this->argument('service').'" invalid');
            return 0;
        }

        $service_name = $this->argument('service');
        $service_namespace = strpos($service_name, '\\') !== false ? $service_name : 'App\Http\Controllers\Api\\'.$service_name;

        if(!class_exists($service_namespace)) {
            $this->error('Service "'.$this->argument('service').'" was not found');
            return -1;
        }

        $reflector = new \ReflectionClass($service_namespace);
        if($reflector->getNamespaceName() === 'App\Http\Controllers\Api') {
            $service_path = dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . pathinfo($reflector->getFileName(), PATHINFO_FILENAME);
        } else {
            $service_path = dirname($reflector->getFileName());
        }

        $service = $service_namespace::SERVICE;
        $guard = $service_namespace::GUARD;

        $version_dirs = glob($service_path . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);

        Artisan::call('permission:cache-reset');

        $checked_groups = [];
        $permission_ids = [];
        foreach($version_dirs as $version_dir_path) {
            $version_dir_name = basename($version_dir_path);
            $version_dir_prefix = substr(basename($version_dir_name), 0, 7);
            if($version_dir_prefix !== 'Version') {
                continue;
            }

            $files = glob($version_dir_path . DIRECTORY_SEPARATOR . '*.{php}', GLOB_BRACE);

            foreach ($files as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);

                $namespace = implode('\\', [$service_namespace, $version_dir_name, $filename]);
                $group = $namespace::group();
                $permission_title = $namespace::title();
                $permission_name = trim($service . $filename);

                if(!isset($checked_groups[$group->name])) {
                    $checked_groups[$group->name] = AclGroups::where('name', '=', $group->name)
                        ->where('guard_name', '=', $guard)
                        ->first();
                    if(!$checked_groups[$group->name]) {
                        $checked_groups[$group->name] = AclGroups::create([
                            'name' => $group->name,
                            'title' => trim($group->title),
                            'guard_name' => $guard,
                            'position' => AclGroups::newPosition($guard)
                        ]);
                    }
                }

                if(!$permission_title) {
                    $permission_title = Strings::splitAtCapitalLetters($filename);
                    $param_name = Strings::splitAtCapitalLetters($group->name);
                    $permission_title_substr = trim(substr($permission_title, 0, strlen($param_name) + 1));
                    if(strtolower($permission_title_substr) === strtolower($param_name)) {
                        $permission_title = trim(substr($permission_title, strlen($param_name)));
                    }
                    $permission_title = ucfirst(strtolower($permission_title));
                }

                $perm = Permissions::upsert($permission_name, $permission_title, $checked_groups[$group->name]->id, $guard);
                $permission_ids[] = $perm->id;
            }
        }

        Permissions::where(function ($q) use ($permission_ids) {
                if (!empty($permission_ids)) {
                    $q->whereNotIn('id', $permission_ids);
                }
            })
            ->delete();

        $this->info('ACL permissions compiled successfully.');
        return 1;
    }
}
