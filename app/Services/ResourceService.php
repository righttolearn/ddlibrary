<?php

namespace App\Services;

use App\Enums\TaxonomyVocabularyEnum;
use App\Models\Resource;
use App\Models\ResourceAttachment;
use App\Models\ResourceAuthor;
use App\Models\ResourceCopyrightHolder;
use App\Models\ResourceCreativeCommon;
use App\Models\ResourceEducationalResource;
use App\Models\ResourceEducationalUse;
use App\Models\ResourceFile;
use App\Models\ResourceIamAuthor;
use App\Models\ResourceKeyword;
use App\Models\ResourceLearningResourceType;
use App\Models\ResourceLevel;
use App\Models\ResourcePublisher;
use App\Models\ResourceSharePermission;
use App\Models\ResourceSubjectArea;
use App\Models\ResourceTranslationRight;
use App\Models\ResourceTranslator;
use App\Models\TaxonomyTerm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ResourceService
{
    /**
     * @throws Throwable
     */
    public function save(array $data, ?int $resourceId = null): Resource
    {
        return DB::transaction(function () use ($data, $resourceId) {
            $resource = $this->saveResource($data, $resourceId);
            $this->saveAttachments($resource, $data);
            $this->saveSubjectAreas($resource, $data);
            $this->saveKeywords($resource, $data);
            $this->saveAuthors($resource, $data);
            $this->savePublishers($resource, $data);
            $this->saveTranslators($resource, $data);
            $this->saveLearningTypes($resource, $data);
            $this->saveEducationalUse($resource, $data);
            $this->saveLevels($resource, $data);
            $this->saveRights($resource, $data);
            $this->saveCopyrightHolder($resource, $data);
            $this->saveLicense($resource, $data);
            return $resource;
        });
    }

    private function saveResource(array $data, ?int $resourceId): Resource
    {
        $resource = $resourceId ? Resource::findOrFail($resourceId) : new Resource;

        $resource->title            = $data['title'];
        $resource->abstract         = $data['abstract'];
        $resource->language         = $data['language'];
        $resource->resource_file_id = $data['resource_file_id'];
        $resource->status           = $data['published'] ?? 0;
        $resource->user_id          = Auth::id();
        $resource->published_at     = now();
        $resource->save();

        // Set tnid on create
        if (!$resourceId) {
            $resource->tnid = $resource->id;
            $resource->save();
        }

        // Link resource file (image)
        ResourceFile::where(['id' => $resource->resource_file_id, 'resource_id' => null])
            ->update(['resource_id' => $resource->id]);

        return $resource;
    }

    private function saveAttachments(Resource $resource, array $data): void
    {
        if (empty($data['attachments'])) return;

        foreach ($data['attachments'] as $attachment) {
            $extension = $attachment->getClientOriginalExtension();
            $fileName  = Auth::id().'_'.uniqid().'_'.time().'.'.$extension;

            Storage::put('resources/'.$fileName, file_get_contents($attachment));

            ResourceAttachment::create([
                'resource_id' => $resource->id,
                'file_name'   => $fileName,
                'file_mime'   => $attachment->getMimeType(),
                'file_size'   => $attachment->getSize(),
            ]);
        }
    }

    private function saveSubjectAreas(Resource $resource, array $data): void
    {
        ResourceSubjectArea::where('resource_id', $resource->id)->delete();

        foreach ($data['subject_areas'] as $subjectId) {
            ResourceSubjectArea::create([
                'resource_id' => $resource->id,
                'tid'         => $subjectId,
            ]);
        }
    }

    private function saveKeywords(Resource $resource, array $data): void
    {
        ResourceKeyword::where('resource_id', $resource->id)->delete();

        if (empty($data['keywords'])) return;

        $keywords = array_filter(array_map('trim', explode(',', $data['keywords'])));

        foreach ($keywords as $kw) {
            $taxonomy = TaxonomyTerm::firstOrCreate(
                ['name' => $kw, 'vid' => TaxonomyVocabularyEnum::Keywords->value],
                ['language' => $data['language']]
            );

            ResourceKeyword::create([
                'resource_id' => $resource->id,
                'tid'         => $taxonomy->id,
            ]);
        }
    }

    private function saveAuthors(Resource $resource, array $data): void
    {
        ResourceAuthor::where('resource_id', $resource->id)->delete();

        if (empty($data['author'])) return;

        $authors = array_filter(array_map('trim', explode(',', $data['author'])));

        foreach ($authors as $author) {
            $taxonomy = TaxonomyTerm::firstOrCreate(
                ['name' => $author, 'vid' => TaxonomyVocabularyEnum::ResourceAuthor->value],
                ['language' => $data['language']]
            );

            ResourceAuthor::create([
                'resource_id' => $resource->id,
                'tid'         => $taxonomy->id,
            ]);
        }
    }

    private function savePublishers(Resource $resource, array $data): void
    {
        ResourcePublisher::where('resource_id', $resource->id)->delete();

        if (empty($data['publisher'])) return;

        $taxonomy = TaxonomyTerm::firstOrCreate(
            ['name' => trim($data['publisher']), 'vid' => TaxonomyVocabularyEnum::ResourcePublisher->value],
            ['language' => $data['language']]
        );

        ResourcePublisher::create([
            'resource_id' => $resource->id,
            'tid'         => $taxonomy->id,
        ]);
    }

    private function saveTranslators(Resource $resource, array $data): void
    {
        ResourceTranslator::where('resource_id', $resource->id)->delete();

        if (empty($data['translator'])) return;

        $translators = array_filter(array_map('trim', explode(',', $data['translator'])));

        foreach ($translators as $translator) {
            $taxonomy = TaxonomyTerm::firstOrCreate(
                ['name' => $translator, 'vid' => TaxonomyVocabularyEnum::ResourceTranslator->value],
                ['language' => $data['language']]
            );

            ResourceTranslator::create([
                'resource_id' => $resource->id,
                'tid'         => $taxonomy->id,
            ]);
        }
    }

    private function saveLearningTypes(Resource $resource, array $data): void
    {
        ResourceLearningResourceType::where('resource_id', $resource->id)->delete();

        foreach ($data['learning_resources_types'] as $typeId) {
            ResourceLearningResourceType::create([
                'resource_id' => $resource->id,
                'tid'         => $typeId,
            ]);
        }
    }

    private function saveEducationalUse(Resource $resource, array $data): void
    {
        ResourceEducationalUse::where('resource_id', $resource->id)->delete();

        foreach ($data['educational_use'] as $useId) {
            ResourceEducationalUse::create([
                'resource_id' => $resource->id,
                'tid'         => $useId,
            ]);
        }
    }

    private function saveLevels(Resource $resource, array $data): void
    {
        ResourceLevel::where('resource_id', $resource->id)->delete();

        foreach ($data['level'] as $levelId) {
            ResourceLevel::create([
                'resource_id' => $resource->id,
                'tid'         => $levelId,
            ]);
        }
    }

    private function saveRights(Resource $resource, array $data): void
    {
        // Clear all three
        ResourceTranslationRight::where('resource_id', $resource->id)->delete();
        ResourceEducationalResource::where('resource_id', $resource->id)->delete();
        ResourceIamAuthor::where('resource_id', $resource->id)->delete();

        if (empty($data['resource_rights'])) return;

        match ($data['resource_rights']) {
            'translation'  => ResourceTranslationRight::create(['resource_id' => $resource->id, 'value' => 1]),
            'educational'  => ResourceEducationalResource::create(['resource_id' => $resource->id, 'value' => 1]),
            'author'       => ResourceIamAuthor::create(['resource_id' => $resource->id, 'value' => 1]),
        };
    }

    private function saveCopyrightHolder(Resource $resource, array $data): void
    {
        ResourceCopyrightHolder::where('resource_id', $resource->id)->delete();

        if (empty($data['copyright_holder'])) return;

        ResourceCopyrightHolder::create([
            'resource_id' => $resource->id,
            'value'       => $data['copyright_holder'],
        ]);
    }

    private function saveLicense(Resource $resource, array $data): void
    {
        ResourceCreativeCommon::where('resource_id', $resource->id)->delete();
        ResourceSharePermission::where('resource_id', $resource->id)->delete();

        if (!empty($data['creative_commons'])) {
            ResourceCreativeCommon::create([
                'resource_id' => $resource->id,
                'tid'         => $data['creative_commons'],
            ]);
        }

        if (!empty($data['creative_commons_other'])) {
            ResourceSharePermission::create([
                'resource_id' => $resource->id,
                'tid'         => $data['creative_commons_other'],
            ]);
        }
    }
}
