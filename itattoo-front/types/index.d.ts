export {};

type DocumentTypes = 'card_id' | 'driving_license' | 'passport' | 'other';
type StatusTypes =
  | null
  | 'canceled'
  | 'deposit'
  | 'not_presented'
  | 'completed_unpaid'
  | 'completed_paid';

declare global {
  type Country = {
    id: number;
    name: string;
  };
  type Currency = {
    id: number;
    name: string;
    code: string;
  };
  type Tag = {
    id: number;
    name: string;
  };
  type Category = {
    id: number;
    name: string;
    services?: Array<Service>;
  };
  type SignedDocument = {
    id: number;
    path: string;
  };
  type Project = {
    id: number;
    name: string;
    description: string;
    category_id: number;
    customer_id: number;
    staff_id: number;
    signed_document?: SignedDocument | null;
  };
  type NotificationEvent =
    | 'created'
    | 'edited'
    | 'canceled'
    | 'remind'
    | 'after';
  type NotificationSettings = {
    id: number;
    customer_events: Array<NotificationEvent>;
    customer_ics_file: boolean;
    customer_link_to_cancel: boolean;
    customer_sms_reminders: boolean;
    customer_email_reminders: boolean;
    customer_deposit_only_sms_reminder: boolean;
    customer_post_appointment_sms: boolean;
    customer_post_appointment_email: boolean;
    customer_pre_appointment_minutes: number;
    customer_post_appointment_minutes: number;
    staff_events: Array<NotificationEvent>;
    staff_sms_reminders: boolean;
    staff_email_reminders: boolean;
    staff_post_appointment_sms: boolean;
    staff_post_appointment_email: boolean;
    staff_pre_appointment_minutes: number;
    staff_post_appointment_minutes: number;
  };
  type CalendarSettings = {
    id: number;
    default_view: string;
    from_time: string;
    to_time: string;
    start_time: string;
    slot_duration: number;
    snap_duration: number;
    hidden_days: Array<number>;
    date_format: string;
    allow_off_hours_booking: boolean;
    allow_double_booking: boolean;
    apply_staff_appearance: boolean;
    use_staff_colors: boolean;
  };
  type Subscription = {
    id: number;
    plan_id: number;
    started_at: string;
    ends_at: string;
    canceled_at: string;
  };
  type Language = {
    id: number;
    iso: string;
    locale: string;
    name: string;
  };
  type Organisation = {
    id: number;
    name: string;
    slug: string;
    language_id: number;
    currency_id: number;
    country_id: number;
    owner_id: number;
    cancellation_buffer_days: number;
    autodelete_period_days: number;
    hidden_fields: Array<string>;
    adult_age: number;
    default_for_auth_user: boolean;
    calendarSettings: CalendarSettings;
    currency: Currency;
    language: Language;
    notificationSettings: NotificationSettings | null;
    activeSubscription?: Subscription | null;
  };
  type AuthUserData = {
    id?: number;
    default_organisation_id?: number;
    first_name?: string;
    last_name?: string;
    email?: string;
    permissions: Array;
    email_verified_at?: any;
    organisations?: Array<Organisation>;
  };
  type ProjectGroup = {
    id: number;
    index: number;
    date: string;
    start_time: string;
    deposit: number;
    project_name: string;
    project_category: string;
  };
  type Customer = {
    id: number;
    email: string;
    image?: string;
    first_name?: string;
    full_name?: string;
    last_name?: string;
    phone_number?: string;
    birth_date?: string;
    gender: string;
    is_minor?: boolean;
    city?: string;
    address?: string;
    state?: string;
    country_id?: number;
    postal_code?: string;
    doc_type?: DocumentTypes;
    issued_by: string;
    doc_no: string;
    expiry_date: string;
    projects?: Array<Project>;
    appointments?: Array<Appointment>;
    media?: Array;
  };

  type Service = {
    id: number;
    name: string;
    duration: number;
    image: string;
    price: number;
    description: string;
    color: string;
    position: number;
    buffer_time: number;
    is_private: boolean;
    hide_from_statistics: boolean;
    is_hourly_rated: boolean;
    organisation_id: number;
    category_id: number;
    is_online?: null | boolean;
  };
  type StudioLocation = {
    id: number;
    country_id: number;
    name: string;
    phone_number: string;
    address: string;
    city: string;
    state: string;
    post_code: string;
    vat_number: string;
    website: string;
    from_time: string;
    to_time: string;
    country: Country;
    organisation: Organisation;
    default_for_auth_user: boolean;
  };
  type Availability = {
    id: number;
    start_time: string;
    end_time: string;
    day: number;
    is_available: boolean;
    location_id: number;
  };
  type TimeOff = {
    id: number;
    reason: string;
    start_date: string;
    end_date: string;
    staff_id: number;
    organisation_id: number;
    location_id: number;
    is_convention: number;
  };
  type Staff = {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string;
    description: string;
    phone_number: string;
    photo: string;
    color: string;
    view_statistics: Boolean;
    is_guest: Boolean;
    is_super_admin?: Boolean;
    email_verified_at: string;
    default_organisation_id: number;
    default_location_id: number;
    organisations?: Array<Organisation>;
    locations?: Array<StudioLocation>;
    availability?: Array<Availability>;
    time_off?: Array<TimeOff>;
    tags?: Array<Tag>;
    services?: Array<Service>;
  };
  type Appointment = {
    id?: number;
    staff_id: number | null;
    customer_id: number | null;
    location_id: number | null;
    service_id: number | null;
    project_id: number | null;
    date: string;
    start_time: string;
    end_time: string;
    duration: number;
    note: string | null;
    price: number | null;
    status: StatusTypes;
    service: Service;
    is_online: boolean;
    project?: Project;
    customer: Customer;
    location: StudioLocation;
    staff: Staff;
    project_group: Array<ProjectGroup>;
    paid_by: string | null;
    deposit: number | null;
  };
  type CalendarEventExtendedProp = {
    isBreak: boolean;
    phone_number?: string;
    note?: string;
    price?: number;
    status?: StatusTypes;
    serviceName?: string;
    staff_id?: number;
    is_online?: boolean;
    vip_name?: string;
    vip_color?: string;
    project_group: Array<ProjectGroup>;
  };
  type CustomerFilters = {
    staff_ids: Array<number>;
    query: string | null;
    order_by: 'first_name' | 'created_at';
    sort_by: 'asc' | 'desc';
    // location_id: number | null;
  };
  type AppointmentFilters = {
    staff_ids: Array<number>;
    from?: string | null;
    to?: string | null;
    customer_ids: Array<number>;
    location_id: number | null;
    service_ids: Array<number>;
    status: StatusTypes;
    duration: number | null;
    duration_operator: string;
  };
  type CalendarEvent = {
    id: number | string;
    title: string;
    start: Date;
    end: Date;
    resourceId: number;
    backgroundColor?: string;
    borderColor?: string;
    textColor?: string;
    display?: string;
    extendedProps: CalendarEventExtendedProp;
  };
}
