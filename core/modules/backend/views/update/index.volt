{% extends 'layouts/layout.volt' %}
{% block title %}Update core{% endblock %}
{% block content %}
    <div class="m-b-md">
        <h3 class="m-b-none">{{ t('Phanbook Updates') }}</h3>
    </div>
    <div class="panel-body">

    <pre>
     .  ____  .    ____________________________
     |/      \|   |                            |
    [| <span style="color: #FF0000;">&hearts;    &hearts;</span> |]  | Git Update Script v0.1 |
     |___==___|  /       &copy; phanbook 2015       |
                  |____________________________|

    {{ output }}
    </pre>
    </div>
{% endblock %}
