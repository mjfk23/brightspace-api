<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Model;

use Gadget\Io\Cast;

final class User
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            OrgId: Cast::toInt($values['OrgId'] ?? null),
            UserId: Cast::toInt($values['UserId'] ?? null),
            OrgDefinedId: Cast::toValueOrNull($values['OrgDefinedId'] ?? null, Cast::toString(...)),
            FirstName: Cast::toString($values['FirstName'] ?? null),
            MiddleName: Cast::toValueOrNull($values['MiddleName'] ?? null, Cast::toString(...)),
            LastName: Cast::toString($values['LastName'] ?? null),
            UserName: Cast::toString($values['UserName'] ?? null),
            ExternalEmail: Cast::toValueOrNull($values['ExternalEmail'] ?? null, Cast::toString(...)),
            UniqueIdentifier: Cast::toString($values['UniqueIdentifier'] ?? null),
            Activation: UserActivation::create($values['Activation'] ?? null),
            DisplayName: Cast::toString($values['DisplayName'] ?? null),
            LastAccessedDate: Cast::toValueOrNull(
                $values['LastAccessedDate'] ?? null,
                fn(mixed $v): \DateTimeInterface => new \DateTime(Cast::toString($v))
            ),
            Pronouns: Cast::toString($values['Pronouns'] ?? null)
        );
    }


    public function __construct(
        public int $OrgId = 0,
        public int $UserId = 0,
        public string|null $OrgDefinedId = null,
        public string $FirstName = '',
        public string|null $MiddleName = null,
        public string $LastName = '',
        public string $UserName = '',
        public string|null $ExternalEmail = null,
        public string $UniqueIdentifier = '',
        public UserActivation $Activation = new UserActivation(true),
        public string $DisplayName = '',
        public \DateTimeInterface|null $LastAccessedDate = null,
        public string|null $Pronouns = '',
        public int $RoleId = 0,
        public bool $SendCreationEmail = false
    ) {
    }


    /**
     * @return array{
     *   OrgDefinedId:string|null,
     *   FirstName:string,
     *   MiddleName:string|null,
     *   LastName:string,
     *   UserName:string,
     *   ExternalEmail:string|null,
     *   Pronouns:string|null
     * }
     */
    private function getCommonPayload(): array
    {
        return [
            'OrgDefinedId' => $this->OrgDefinedId,
            'FirstName' => $this->FirstName,
            'MiddleName' => $this->MiddleName,
            'LastName' => $this->LastName,
            'UserName' => $this->UserName,
            'ExternalEmail' => $this->ExternalEmail,
            'Pronouns' => $this->Pronouns
        ];
    }


    /**
     * @return array{
     *   OrgDefinedId:string|null,
     *   FirstName:string,
     *   MiddleName:string|null,
     *   LastName:string,
     *   UserName:string,
     *   ExternalEmail:string|null,
     *   Pronouns:string|null,
     *   IsActive:bool,
     *   RoleId:int,
     *   SendCreationEmail:bool
     * }
     */
    public function getCreatePayload(): array
    {
        return [
            ...$this->getCommonPayload(),
            'IsActive' => $this->Activation->IsActive,
            'RoleId' => $this->RoleId,
            'SendCreationEmail' => $this->SendCreationEmail
        ];
    }


    /**
     * @return array{
     *   OrgDefinedId:string|null,
     *   FirstName:string,
     *   MiddleName:string|null,
     *   LastName:string,
     *   UserName:string,
     *   ExternalEmail:string|null,
     *   Pronouns:string|null,
     *   Activation:UserActivation
     * }
     */
    public function getUpdatePayload(): array
    {
        return [
            ...$this->getCommonPayload(),
            'Activation' => $this->Activation
        ];
    }
}
