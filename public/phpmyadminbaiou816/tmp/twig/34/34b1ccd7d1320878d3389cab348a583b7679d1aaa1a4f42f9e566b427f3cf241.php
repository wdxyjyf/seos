<?php

/* server/binlog/log_selector.twig */
class __TwigTemplate_fd26f2ff79e8d6dbc73a614166f0041e0008fc86f349c4f6baedec3ef23596ab extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<form action=\"server_binlog.php\" method=\"get\">
    ";
        // line 2
        echo PhpMyAdmin\Url::getHiddenInputs(($context["url_params"] ?? null));
        echo "
    <fieldset>
        <legend>
            ";
        // line 5
        echo _gettext("Select binary log to view");
        // line 6
        echo "        </legend>
        ";
        // line 7
        $context["full_size"] = 0;
        // line 8
        echo "        <select name=\"log\">
            ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["binary_logs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["each_log"]) {
            // line 10
            echo "                <option value=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["each_log"], "Log_name", array(), "array"), "html", null, true);
            echo "\"";
            // line 11
            echo ((($this->getAttribute($context["each_log"], "Log_name", array(), "array") == ($context["log"] ?? null))) ? (" selected=\"selected\"") : (""));
            echo ">
                    ";
            // line 12
            echo twig_escape_filter($this->env, $this->getAttribute($context["each_log"], "Log_name", array(), "array"), "html", null, true);
            echo "
                    ";
            // line 13
            if ($this->getAttribute($context["each_log"], "File_size", array(), "array", true, true)) {
                // line 14
                echo "                        (";
                echo twig_escape_filter($this->env, twig_join_filter(PhpMyAdmin\Util::formatByteDown($this->getAttribute($context["each_log"], "File_size", array(), "array"), 3, 2), " "), "html", null, true);
                echo ")
                        ";
                // line 15
                $context["full_size"] = (($context["full_size"] ?? null) + $this->getAttribute($context["each_log"], "File_size", array(), "array"));
                // line 16
                echo "                    ";
            }
            // line 17
            echo "                </option>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['each_log'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 19
        echo "        </select>
        ";
        // line 20
        echo twig_escape_filter($this->env, twig_length_filter($this->env, ($context["binary_logs"] ?? null)), "html", null, true);
        echo "
        ";
        // line 21
        echo _gettext("Files");
        echo ",
        ";
        // line 22
        if ((($context["full_size"] ?? null) > 0)) {
            // line 23
            echo "            ";
            echo twig_escape_filter($this->env, twig_join_filter(PhpMyAdmin\Util::formatByteDown(($context["full_size"] ?? null)), " "), "html", null, true);
            echo "
        ";
        }
        // line 25
        echo "    </fieldset>
    <fieldset class=\"tblFooters\">
        <input type=\"submit\" value=\"";
        // line 27
        echo _gettext("Go");
        echo "\" />
    </fieldset>
</form>
";
    }

    public function getTemplateName()
    {
        return "server/binlog/log_selector.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  96 => 27,  92 => 25,  86 => 23,  84 => 22,  80 => 21,  76 => 20,  73 => 19,  66 => 17,  63 => 16,  61 => 15,  56 => 14,  54 => 13,  50 => 12,  46 => 11,  42 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,  28 => 5,  22 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "server/binlog/log_selector.twig", "/www/seos/public/phpmyadminbaiou816/templates/server/binlog/log_selector.twig");
    }
}
